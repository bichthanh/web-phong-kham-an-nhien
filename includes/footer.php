    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Col 1: Brand & Giới thiệu -->
                <div class="footer-col">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;">
                        <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="Phòng Khám Đa Khoa An Nhiên" style="height:60px;width:60px;border-radius:50%;background:#FFF;padding:2px;box-shadow:0 4px 10px rgba(0,0,0,0.3);">
                        <div>
                            <strong style="color:#FFF;font-size:16px;display:block;letter-spacing:0.5px;">ĐA KHOA AN NHIÊN</strong>
                            <span style="color:#2DD4BF;font-size:12px;font-weight:600;">CÔNG TY TNHH Y DƯỢC AN NHIÊN</span>
                        </div>
                    </div>
                    <p style="font-size:13.5px;line-height:1.7;">
                        Cơ sở thực hành lâm sàng, khám chữa bệnh và chuyển giao công nghệ thuộc hệ sinh thái Viện Nghiên cứu và Đào tạo Y Dược Việt – Hàn (VKIM). Địa chỉ khám chữa bệnh uy tín, tận tâm hàng đầu tại Nam Định.
                    </p>
                    <div style="display:flex;gap:10px;margin-top:16px;flex-wrap:wrap;">
                        <span class="badge badge-success"><i class="fa-solid fa-id-card"></i> BHYT Thông Tuyến</span>
                        <span class="badge badge-info"><i class="fa-solid fa-droplet"></i> Thận nhân tạo VKIM</span>
                    </div>
                </div>

                <!-- Col 2: Liên kết nhanh -->
                <div class="footer-col">
                    <h4>Khám & Điều Trị</h4>
                    <ul class="footer-links">
                        <li><a href="<?= BASE_URL ?>/index.php"><i class="fa-solid fa-angle-right"></i> Trang chủ phòng khám</a></li>
                        <li><a href="<?= BASE_URL ?>/chuyen-khoa.php"><i class="fa-solid fa-angle-right"></i> 8 Chuyên khoa mũi nhọn</a></li>
                        <li><a href="<?= BASE_URL ?>/bac-si.php"><i class="fa-solid fa-angle-right"></i> Danh sách bác sĩ chuyên khoa</a></li>
                        <li><a href="<?= BASE_URL ?>/dat-lich.php"><i class="fa-solid fa-angle-right"></i> Đặt lịch khám online 3 bước</a></li>
                        <li><a href="<?= BASE_URL ?>/tra-cuu.php"><i class="fa-solid fa-angle-right"></i> Tra cứu phiếu & In đơn thuốc</a></li>
                        <li><a href="<?= BASE_URL ?>/cam-nang.php"><i class="fa-solid fa-angle-right"></i> Cẩm nang sức khỏe y khoa</a></li>
                    </ul>
                </div>

                <!-- Col 3: Thông tin liên hệ Nam Định -->
                <div class="footer-col">
                    <h4>Địa Chỉ Tại Nam Định</h4>
                    <ul class="footer-contact">
                        <li>
                            <i class="fa-solid fa-location-dot"></i>
                            <div>
                                <strong>Cơ sở 1:</strong> Khu Bồi Tây, Xã Mỹ Phúc, Huyện Mỹ Lộc, Tỉnh Nam Định<br>
                                <strong>Cơ sở 2:</strong> Đường Trần Tự Khánh, TP. Nam Định
                            </div>
                        </li>
                        <li>
                            <i class="fa-solid fa-phone"></i>
                            <div>
                                Hotline tiếp đón: <strong>0944.809.221</strong><br>
                                Cấp cứu 24/7 & Thận nhân tạo: <strong>0983.301.202</strong>
                            </div>
                        </li>
                        <li>
                            <i class="fa-solid fa-envelope"></i>
                            <div>Email: lienhe@dakhoaannhien.vn</div>
                        </li>
                    </ul>
                </div>

                <!-- Col 4: Thông tin đồ án Nhóm 12 UTT -->
                <div class="footer-col">
                    <h4>Đồ Án Nhóm 12 (UTT)</h4>
                    <p style="font-size:13px;color:#CBD5E1;margin-bottom:10px;">
                        Khoa Công nghệ Thông tin<br>
                        <strong>Trường ĐH Công nghệ Giao thông Vận tải</strong>
                    </p>
                    <div class="footer-team-box">
                        <h5><i class="fa-solid fa-code"></i> Nhóm 12 • Lớp 74DCTT26</h5>
                        <ul>
                            <li>• 32. Nguyễn Ngọc Mỹ</li>
                            <li>• 07. Nguyễn Hồng Đăng</li>
                            <li>• 49. Vũ Thị Minh Thư</li>
                            <li>• 57. Trần Thị Hồng Xoan</li>
                            <li>• 46. Lưu Thị Bích Thanh</li>
                        </ul>
                        <p style="margin-top:6px;font-size:12px;color:#94A3B8;">
                            GVHD: <strong>Cô Phạm Thị Thuận</strong>
                        </p>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <div>
                    © 2026 <strong>Phòng Khám Đa Khoa An Nhiên Nam Định</strong>. Bản quyền thuộc về Đồ án Nhóm 12 (KTPM - UTT).
                </div>
                <div>
                    Công nghệ: HTML5 • CSS3 • JavaScript • PHP 8.2 • MySQL
                </div>
            </div>
        </div>
    </footer>

    <!-- Main JavaScript -->
    <script>
        const BASE_URL = '<?= BASE_URL ?>';
    </script>
    <script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
