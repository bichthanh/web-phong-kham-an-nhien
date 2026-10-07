/**
 * JavaScript tương tác cho Phòng khám Đa khoa An Nhiên
 * Nhóm 12 - Lớp 74DCTT26 - ĐH Công nghệ Giao thông Vận tải
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Mobile Menu Toggle
    const mobileBtn = document.querySelector('.mobile-toggle');
    const navMenu = document.querySelector('.nav-menu');
    if (mobileBtn && navMenu) {
        mobileBtn.addEventListener('click', function () {
            navMenu.classList.toggle('show');
        });
    }

    // 2. Interactive Booking Wizard (Chọn Chuyên khoa -> Bác sĩ -> Ngày & Giờ)
    const specialtySelect = document.getElementById('booking_specialty');
    const doctorSelect = document.getElementById('booking_doctor');
    const dateInput = document.getElementById('booking_date');
    const slotsContainer = document.getElementById('booking_slots_container');
    const scheduleIdInput = document.getElementById('booking_schedule_id');
    const appointmentTimeInput = document.getElementById('booking_time');

    // Khi chọn chuyên khoa -> Lọc danh sách bác sĩ tương ứng
    if (specialtySelect && doctorSelect) {
        specialtySelect.addEventListener('change', function () {
            const specId = this.value;
            doctorSelect.innerHTML = '<option value="">-- Đang tải danh sách bác sĩ... --</option>';

            if (!specId) {
                doctorSelect.innerHTML = '<option value="">-- Vui lòng chọn chuyên khoa trước --</option>';
                if (slotsContainer) slotsContainer.innerHTML = '<p class="text-muted">Vui lòng chọn bác sĩ và ngày khám để xem các ca trực trống.</p>';
                return;
            }

            const apiBase = (typeof BASE_URL !== 'undefined') ? BASE_URL : '';
            fetch(`${apiBase}/api/get_doctors_by_specialty.php?specialty_id=${specId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.doctors.length > 0) {
                        let html = '<option value="">-- Chọn bác sĩ khám --</option>';
                        data.doctors.forEach(doc => {
                            html += `<option value="${doc.id}" data-fee="${doc.consultation_fee}">
                                ${doc.degree ? doc.degree + ' ' : ''}${doc.full_name} (${doc.clinic_room}) - ${Number(doc.consultation_fee).toLocaleString('vi-VN')} đ
                            </option>`;
                        });
                        doctorSelect.innerHTML = html;
                    } else {
                        doctorSelect.innerHTML = '<option value="">-- Không có bác sĩ trực thuộc khoa này --</option>';
                    }
                    if (slotsContainer) slotsContainer.innerHTML = '<p class="text-muted">Vui lòng chọn bác sĩ và ngày khám để xem các ca trực trống.</p>';
                })
                .catch(err => {
                    console.error(err);
                    doctorSelect.innerHTML = '<option value="">-- Lỗi tải danh sách bác sĩ --</option>';
                });
        });
    }

    // Khi chọn Bác sĩ hoặc Ngày khám -> Tải danh sách ca trực / slot trống
    function loadAvailableSchedules() {
        if (!doctorSelect || !dateInput || !slotsContainer) return;
        const docId = doctorSelect.value;
        const dateVal = dateInput.value;

        if (!docId || !dateVal) {
            slotsContainer.innerHTML = '<p class="text-muted"><i class="fa-solid fa-circle-info"></i> Vui lòng chọn bác sĩ và ngày khám để hiển thị ca trực còn trống.</p>';
            return;
        }

        slotsContainer.innerHTML = '<div style="text-align:center;padding:20px;"><i class="fa-solid fa-spinner fa-spin fa-2x" style="color:var(--primary)"></i><p style="margin-top:8px;">Đang tải lịch khám khả dụng...</p></div>';

        const apiBase = (typeof BASE_URL !== 'undefined') ? BASE_URL : '';
        fetch(`${apiBase}/api/get_schedules_by_doctor.php?doctor_id=${docId}&date=${dateVal}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.schedules.length > 0) {
                    let html = '<div class="time-slots-grid">';
                    data.schedules.forEach(item => {
                        const isFull = (item.current_booked >= item.max_patients) || (item.status === 'FULL');
                        const disabledAttr = isFull ? 'disabled' : '';
                        const badgeText = isFull ? 'Hết chỗ' : `Còn ${item.max_patients - item.current_booked} chỗ`;
                        const badgeClass = isFull ? 'color:#EF4444' : 'color:#10B981';

                        html += `
                            <button type="button" class="time-slot-btn" ${disabledAttr} 
                                    data-schedule-id="${item.id}" 
                                    data-time="${item.start_time.substring(0, 5)}">
                                <span class="slot-time">${item.session_name}</span>
                                <span style="font-size:12.5px;color:#0F172A;font-weight:600;">${item.start_time.substring(0, 5)} - ${item.end_time.substring(0, 5)}</span>
                                <span class="slot-badge" style="${badgeClass};font-weight:600;">${badgeText}</span>
                            </button>
                        `;
                    });
                    html += '</div>';
                    slotsContainer.innerHTML = html;

                    // Gắn sự kiện click chọn ca khám
                    document.querySelectorAll('.time-slot-btn:not(:disabled)').forEach(btn => {
                        btn.addEventListener('click', function () {
                            document.querySelectorAll('.time-slot-btn').forEach(b => b.classList.remove('selected'));
                            this.classList.add('selected');
                            if (scheduleIdInput) scheduleIdInput.value = this.getAttribute('data-schedule-id');
                            if (appointmentTimeInput) appointmentTimeInput.value = this.getAttribute('data-time');
                        });
                    });
                } else {
                    slotsContainer.innerHTML = `
                        <div style="background:#FFFBEB;border:1px solid #FCD34D;color:#B45309;padding:16px;border-radius:8px;">
                            <i class="fa-solid fa-triangle-exclamation"></i> Bác sĩ không có ca trực trong ngày này hoặc đã kín lịch. Vui lòng chọn ngày khác!
                        </div>
                    `;
                }
            })
            .catch(err => {
                console.error(err);
                slotsContainer.innerHTML = '<p class="text-danger">Lỗi khi tải lịch làm việc của bác sĩ.</p>';
            });
    }

    if (doctorSelect) doctorSelect.addEventListener('change', loadAvailableSchedules);
    if (dateInput) dateInput.addEventListener('change', loadAvailableSchedules);

    // 3. Validation form đặt lịch
    const bookingForm = document.getElementById('appointment_booking_form');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function (e) {
            if (!scheduleIdInput || !scheduleIdInput.value) {
                e.preventDefault();
                alert('Vui lòng chọn một ca khám (khung giờ) còn trống của bác sĩ trước khi xác nhận đặt lịch!');
                return false;
            }
        });
    }
});

// Hàm in phiếu khám / đơn thuốc
function printPrescription() {
    window.print();
}
