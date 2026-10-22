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
                if (slotsContainer) slotsContainer.innerHTML = '<p class="text-muted"><i class="fa-solid fa-circle-info"></i> Vui lòng chọn bác sĩ và ngày khám để xem các ca trực trống.</p>';
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
                    if (slotsContainer) slotsContainer.innerHTML = '<p class="text-muted"><i class="fa-solid fa-circle-info"></i> Vui lòng chọn bác sĩ và ngày khám để xem các ca trực trống.</p>';
                })
                .catch(err => {
                    console.error(err);
                    doctorSelect.innerHTML = '<option value="">-- Lỗi tải danh sách bác sĩ --</option>';
                });
        });
    }

    // Khi chọn Bác sĩ hoặc Ngày khám -> Tải danh sách ca trực / slot trống (Quy định 5 người/ca)
    function loadAvailableSchedules() {
        if (!doctorSelect || !dateInput || !slotsContainer) return;
        const docId = doctorSelect.value;
        const dateVal = dateInput.value;

        // Reset inputs
        if (scheduleIdInput) scheduleIdInput.value = '';
        if (appointmentTimeInput) appointmentTimeInput.value = '';

        if (!docId || !dateVal) {
            slotsContainer.innerHTML = '<p class="text-muted"><i class="fa-solid fa-circle-info"></i> Vui lòng chọn bác sĩ và ngày khám để hiển thị ca trực còn trống.</p>';
            return;
        }

        slotsContainer.innerHTML = '<div style="text-align:center;padding:25px;"><i class="fa-solid fa-spinner fa-spin fa-2x" style="color:var(--primary)"></i><p style="margin-top:10px;color:#64748B;">Đang kiểm tra sĩ số và các khung giờ trống...</p></div>';

        const apiBase = (typeof BASE_URL !== 'undefined') ? BASE_URL : '';
        fetch(`${apiBase}/api/get_schedules_by_doctor.php?doctor_id=${docId}&date=${dateVal}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.schedules.length > 0) {
                    let html = '<div style="display:flex;flex-direction:column;gap:18px;">';

                    data.schedules.forEach(item => {
                        const maxPatients = item.max_patients || 8;
                        const isFull = item.is_full || (item.current_booked >= maxPatients);
                        const booked = item.current_booked;
                        const percent = Math.min(100, Math.round((booked / maxPatients) * 100));

                        html += `
                        <div class="session-box" style="border:1.5px solid ${isFull ? '#FECACA' : '#CBD5E1'};border-radius:10px;padding:16px;background:${isFull ? '#FEF2F2' : '#FFFFFF'};box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                            <!-- TIÊU ĐỀ CA & TIẾN ĐỘ -->
                            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:12px;">
                                <div>
                                    <strong style="font-size:15px;color:#0F172A;display:flex;align-items:center;gap:6px;">
                                        <i class="fa-solid ${item.is_morning ? 'fa-sun' : 'fa-cloud-sun'}" style="color:${item.is_morning ? '#EAB308' : '#F97316'};"></i>
                                        ${item.session_name}
                                    </strong>
                                </div>
                                <div>
                                    ${isFull ? `
                                        <span class="badge" style="background:#EF4444;color:#FFF;padding:5px 10px;font-size:12px;font-weight:700;border-radius:6px;">
                                            <i class="fa-solid fa-ban"></i> KÍN LỊCH (${booked}/${maxPatients} người) - Vui lòng chọn ca/ngày khác
                                        </span>
                                    ` : `
                                        <span class="badge" style="background:#ECFDF5;color:#059669;border:1px solid #A7F3D0;padding:5px 10px;font-size:12.5px;font-weight:700;border-radius:6px;">
                                            <i class="fa-solid fa-user-group"></i> Đã đặt: ${booked}/${maxPatients} người (Còn ${item.remaining} chỗ)
                                        </span>
                                    `}
                                </div>
                            </div>

                            <!-- THANH TIẾN ĐỘ SĨ SỐ -->
                            <div style="background:#E2E8F0;height:6px;border-radius:3px;overflow:hidden;margin-bottom:14px;">
                                <div style="background:${isFull ? '#EF4444' : '#10B981'};width:${percent}%;height:100%;transition:width 0.3s ease;"></div>
                            </div>

                            <!-- CÁC KHUNG GIỜ CHI TIẾT (Tối đa 2 người / khung giờ) -->
                            <div style="font-size:12px;color:#64748B;margin-bottom:8px;font-weight:600;display:flex;justify-content:space-between;align-items:center;">
                                <span><i class="fa-regular fa-clock"></i> Chọn khung giờ đến khám mong muốn:</span>
                                <span style="font-size:11px;color:#0D9488;font-weight:600;"><i class="fa-solid fa-circle-info"></i> Tối đa 2 người / khung giờ</span>
                            </div>

                            <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(135px, 1fr));gap:10px;">
                        `;

                        item.time_slots.forEach(slot => {
                            const isSlotFull = slot.is_full || (slot.booked_in_slot >= 2) || isFull;

                            if (isSlotFull) {
                                html += `
                                    <button type="button" class="time-slot-btn disabled" disabled 
                                            style="opacity:0.65;background:#FEF2F2;border:1.5px solid #FCA5A5;color:#991B1B;cursor:not-allowed;padding:10px 8px;text-align:center;border-radius:8px;">
                                        <span style="display:block;font-weight:700;font-size:13px;color:#991B1B;">${slot.label}</span>
                                        <span style="display:inline-block;font-size:11px;background:#FEE2E2;color:#DC2626;padding:2px 6px;border-radius:4px;margin-top:4px;font-weight:600;">
                                            <i class="fa-solid fa-ban"></i> Hết chỗ (${slot.booked_in_slot}/2)
                                        </span>
                                    </button>
                                `;
                            } else {
                                const isAlmostFull = (slot.booked_in_slot === 1);
                                const badgeBg = isAlmostFull ? '#FEF3C7' : '#ECFDF5';
                                const badgeColor = isAlmostFull ? '#D97706' : '#059669';
                                const badgeBorder = isAlmostFull ? '#FDE68A' : '#A7F3D0';
                                const statusText = isAlmostFull ? `Còn 1 chỗ (1/2)` : `Còn 2 chỗ (0/2)`;

                                html += `
                                    <button type="button" class="time-slot-btn" 
                                            data-schedule-id="${item.id}" 
                                            data-time="${slot.full_time}"
                                            data-label="${slot.label}"
                                            style="padding:10px 8px;text-align:center;border-radius:8px;border:1.5px solid ${isAlmostFull ? '#FCD34D' : '#CBD5E1'};background:#FFF;cursor:pointer;transition:all 0.2s;">
                                        <span style="display:block;font-weight:700;font-size:13px;color:#0F172A;">${slot.label}</span>
                                        <span style="display:inline-block;font-size:11px;background:${badgeBg};color:${badgeColor};border:1px solid ${badgeBorder};padding:2px 6px;border-radius:4px;margin-top:4px;font-weight:600;">
                                            ${statusText}
                                        </span>
                                    </button>
                                `;
                            }
                        });

                        html += `
                            </div>
                        </div>
                        `;
                    });

                    html += '</div>';
                    slotsContainer.innerHTML = html;

                    // Gắn sự kiện click chọn khung giờ
                    slotsContainer.querySelectorAll('.time-slot-btn:not(:disabled)').forEach(btn => {
                        btn.addEventListener('click', function () {
                            slotsContainer.querySelectorAll('.time-slot-btn').forEach(b => {
                                b.style.borderColor = '#CBD5E1';
                                b.style.background = '#FFF';
                                b.style.color = '#0F172A';
                                b.classList.remove('selected');
                            });

                            this.classList.add('selected');
                            this.style.borderColor = 'var(--primary)';
                            this.style.background = '#F0FDFA';
                            this.style.boxShadow = '0 0 0 2px var(--primary)';

                            const schId = this.getAttribute('data-schedule-id');
                            const timeVal = this.getAttribute('data-time');
                            const labelVal = this.getAttribute('data-label');

                            if (scheduleIdInput) scheduleIdInput.value = schId;
                            if (appointmentTimeInput) appointmentTimeInput.value = timeVal;

                            // Hiển thị tóm tắt giờ đã chọn
                            let summaryEl = document.getElementById('selected_slot_summary');
                            if (!summaryEl) {
                                summaryEl = document.createElement('div');
                                summaryEl.id = 'selected_slot_summary';
                                summaryEl.style.cssText = 'margin-top:12px;padding:10px 14px;background:#F0FDFA;border:1px solid #99F6E4;border-radius:8px;font-size:13px;color:#0F766E;font-weight:600;display:flex;align-items:center;gap:8px;';
                                slotsContainer.appendChild(summaryEl);
                            }
                            summaryEl.innerHTML = `<i class="fa-solid fa-circle-check" style="color:#0D9488;"></i> Bạn đã chọn: <strong>${labelVal}</strong> (Ngày ${dateVal})`;
                        });
                    });
                } else {
                    slotsContainer.innerHTML = `
                        <div style="background:#FFFBEB;border:1px solid #FCD34D;color:#B45309;padding:16px;border-radius:8px;">
                            <i class="fa-solid fa-triangle-exclamation"></i> Bác sĩ không có ca trực trong ngày này hoặc đã đủ số lượng khám. Vui lòng chọn ngày khác!
                        </div>
                    `;
                }
            })
            .catch(err => {
                console.error(err);
                slotsContainer.innerHTML = '<p class="text-danger"><i class="fa-solid fa-circle-exclamation"></i> Lỗi khi tải dữ liệu lịch khám. Vui lòng thử lại!</p>';
            });
    }

    if (doctorSelect) doctorSelect.addEventListener('change', loadAvailableSchedules);
    if (dateInput) dateInput.addEventListener('change', loadAvailableSchedules);

    // Tự động load nếu đã có sẵn bác sĩ & ngày (preset)
    if (doctorSelect && doctorSelect.value && dateInput && dateInput.value) {
        loadAvailableSchedules();
    }

    // 3. Validation form đặt lịch
    const bookingForm = document.getElementById('appointment_booking_form');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function (e) {
            if (!scheduleIdInput || !scheduleIdInput.value || !appointmentTimeInput || !appointmentTimeInput.value) {
                e.preventDefault();
                alert('Vui lòng bấm chọn một khung giờ khám còn trống (ví dụ: 07:00 - 08:00) trước khi xác nhận đặt lịch!');
                return false;
            }
        });
    }
});

// Hàm in phiếu khám / đơn thuốc
function printPrescription() {
    window.print();
}
