# Checklist test — chuyển HTTP sang SafeHttpClient

**Phạm vi:** Gọi API microservice qua `safeGet` / `safePost` (timeout, retry, log, rewrite URL nội bộ). Xóa các class legacy `Inventory`, `LmsV2`, `Org`, `Systems`, `TestsV2`. Cải thiện log ở `Upload`.

**Môi trường gợi ý:** Staging có `API_MICROSERVICE_URL_V2`, token hợp lệ; tùy chọn bật `API_MICROSERVICE_URL_INTERNAL` để kiểm rewrite URL.

## Kiểm tra chung

Áp dụng cho mọi hàm liệt kê bên dưới:

- [ ] Happy path: response giống trước khi refactor (cấu trúc JSON, mã nghiệp vụ).
- [ ] Token sai / hết hạn: không crash; giá trị fallback đúng (`false`, `[]`, `0` tùy hàm).
- [ ] Microservice timeout / không phản hồi: log có URL; client không treo vô hạn.
- [ ] Lỗi validation 4xx (POST): vẫn nhận body lỗi từ API khi có (theo `safePost`).
- [ ] Có `API_MICROSERVICE_URL_INTERNAL`: request vẫn thành công (Host header).

---

## App

| Hàm | File | Checklist |
|-----|------|-----------|
| `count` | `src/models/App/News.php` | [ ] Đếm tin theo filter; [ ] Filter rỗng; [ ] Lỗi API → `false`. |

---

## Authorization

| Hàm | File | Checklist |
|-----|------|-----------|
| `me` | `src/models/Authorization/EmployeeToRole.php` | [ ] Quyền user đăng nhập; [ ] Cache hit/miss; [ ] Lỗi → `false`. |
| `employee` | ↑ | [ ] Chi tiết quyền theo employee; [ ] Employee không tồn tại. |
| `getAllPermission` | ↑ | [ ] Danh sách permission đầy đủ; [ ] Lỗi → `false`. |

---

## Call Center

| Hàm | File | Checklist |
|-----|------|-----------|
| `close` | `src/models/CallCenter/Ticket.php` | [ ] Đóng ticket hợp lệ; [ ] Ticket đã đóng / không tồn tại. |
| `comment` | ↑ | [ ] Thêm comment; [ ] Payload thiếu field bắt buộc. |
| `assign` | ↑ | [ ] Gán người xử lý; [ ] Gán lại cùng ticket. |
| `action` | ↑ | [ ] Action workflow đúng trạng thái. |
| `storeFollower` | ↑ | [ ] Thêm follower; [ ] Trùng follower. |
| `updateParamProcess` | ↑ | [ ] Cập nhật params process. |
| `destroyFollower` | ↑ | [ ] Xóa follower. |
| `cancel` | ↑ | [ ] Hủy ticket; [ ] Ticket không hủy được. |

---

## Course (E-learning)

| Hàm | File | Checklist |
|-----|------|-----------|
| `list_classes` | `src/models/Course/Students.php` | [ ] Danh sách lớp theo học viên; [ ] Lỗi → `[]`. |
| `list_courses` | ↑ | [ ] Danh sách khóa; [ ] Lỗi → `[]`. |
| `tracking` | ↑ | [ ] Ghi tracking học; [ ] ID không hợp lệ. |
| `register_course` | ↑ | [ ] Đăng ký khóa thành công / báo lỗi nghiệp vụ. |

---

## CRM

| Hàm | File | Checklist |
|-----|------|-----------|
| `getContactId` | `src/models/Crm/Contacts.php` | [ ] Trả về mảng `_id` unique; [ ] Không có contact → `[]`; [ ] Lỗi API → `[]`. |

---

## Finance

| Hàm | File | Checklist |
|-----|------|-----------|
| `send` | `src/models/Finance/Invoices.php` | [ ] Gửi hóa đơn; [ ] Lỗi → `[]`. |
| `send` | `src/models/Finance/PaymentRequest.php` | [ ] Gửi yêu cầu thanh toán. |
| `checkCode` | `src/models/Finance/Vouchers.php` | [ ] Mã hợp lệ / hết hạn / sai mã. |
| `deposit` | `src/models/Finance/WalletTransaction.php` | [ ] Nạp ví thành công. |
| `qr` | ↑ | [ ] Tạo QR; [ ] Tham số thiếu. |
| `confirm_info` | ↑ | [ ] Xác nhận giao dịch theo ID. |

---

## HR

| Hàm | File | Checklist |
|-----|------|-----------|
| `storeFirst` | `src/models/Hr/Device.php` | [ ] Đăng ký thiết bị lần đầu; [ ] Lỗi → `false`. |
| `getEmployeeId` | `src/models/Hr/Document.php` | [ ] Document related employee; [ ] Related department/branch/brand → gọi thêm `/hr/employees`; [ ] Không có related → `[]`. |
| `closeMonthly` | `src/models/Hr/TrackingConfirm.php` | [ ] Chốt tháng; [ ] Lỗi → `false`. |

---

## Inventory

| Hàm | File | Checklist |
|-----|------|-----------|
| `getLogStatus` | `src/models/Inventory/Logs.php` | [ ] Log theo từng `status`; [ ] Exception → `[]`. **Lưu ý:** không còn map riêng 403 → `UnAuthenticated` — kiểm tra UI/xử lý phía trên vẫn đúng. |
| `transaction_detail` | `src/models/Inventory/Transaction.php` | [ ] Chi tiết phiếu theo `transaction_id`; [ ] ID sai → `[]`. |

### Legacy đã xóa

Xác nhận app chỉ dùng model V2, không gọi class cũ:

- [ ] Thay `Inventory::getProducts`, `getStores`, `getInventories` (+ detail) bằng `models/Inventory/*`.
- [ ] Thay `Org::*` bằng `models/Org/*`.
- [ ] Thay `LmsV2::*` bằng `models/Lms/*`.
- [ ] Thay `TestsV2::*` bằng `models/Test/*`.
- [ ] Thay `Systems::pushLogs` bằng luồng log/system mới (nếu còn reference).

---

## LMS

| Hàm | File | Checklist |
|-----|------|-----------|
| `semesterTest` | `src/models/Lms/ClassSchedule.php` | [ ] Tạo/cập nhật kiểm tra học kỳ; [ ] Lỗi nghiệp vụ (message) vẫn hiển thị. |
| `cancelSemesterTest` | ↑ | [ ] Hủy kiểm tra học kỳ. |
| `updateBySchedule` | ↑ | [ ] Cập nhật theo lịch. |
| `storeTicket` | `src/models/Lms/StudentHighScore.php` | [ ] Ghi log high score / ticket. |
| `getContactId` | `src/models/Lms/Students.php` | [ ] Map student → `contact_id`. |
| `examTime` | ↑ | [ ] Cập nhật giờ thi; [ ] Lỗi → `[]`. |
| `in_tutoring` | ↑ | [ ] Trạng thái tutoring. |
| `sendResult` | ↑ | [ ] Gửi kết quả cho lớp/học viên. |

---

## Notification

| Hàm | File | Checklist |
|-----|------|-----------|
| `updateReadStatus` | `src/models/Notification/App.php` | [ ] Đánh dấu đã đọc; [ ] Lỗi → `false`. |
| `send` | `src/models/Notification/Notification.php` | [ ] Gửi thông báo thành công → `true`; [ ] **Lỗi API → `['message' => '']`** (khác trước: body lỗi) — xác nhận product chấp nhận. |

---

## Org

| Hàm | File | Checklist |
|-----|------|-----------|
| `location` | `src/models/Org/Branchs.php` | [ ] Branch theo location/filter; [ ] Lỗi → `false`. |

---

## Recruitment

| Hàm | File | Checklist |
|-----|------|-----------|
| `scorecard` | `src/models/Recruitment/Calendar.php` | [ ] Tạo/cập nhật scorecard theo `relate_id` + `relate_type`. |

---

## Support

| Hàm | File | Checklist |
|-----|------|-----------|
| `count` | `src/models/Support/Comments.php` | [ ] Đếm comment; [ ] Lỗi → `0`. |
| `getComment` | ↑ | [ ] HTML render comment; [ ] Lỗi → `''`. |
| `count_like` | `src/models/Support/Likes.php` | [ ] Đếm like; [ ] Lỗi → `0`. |
| `change` | ↑ | [ ] Like/unlike; [ ] Lỗi → `0`. |
| `completed` | `src/models/Support/Reminder.php` | [ ] Hoàn thành reminder; [ ] Lỗi → `false`. |

---

## Test

| Hàm | File | Checklist |
|-----|------|-----------|
| `createLog` | `src/models/Test/TestLog.php` | [ ] Tạo log bài test; [ ] Lỗi → `false`. |
| `updateLog` | ↑ | [ ] Cập nhật log; [ ] Lỗi → `false`. |

---

## Upload (không dùng SafeHttpClient)

| Hàm | File | Checklist |
|-----|------|-----------|
| `moveFiles` | `src/Upload.php` | [ ] Move file thành công; [ ] Lỗi: log có **URL + body**. |
| `moveAndDeleteFiles` | ↑ | [ ] Move + delete; [ ] Lỗi từng bước: log path `/moves`, `/deletes`. |

---

## Model base (hồi quy nhanh)

Các luồng dùng `Model::all`, `detail`, `create`, `update`, `remove` (đã qua `safeGet` / `safePost` / `safePut` / `safeDelete`):

- [ ] CRUD một entity bất kỳ (GET list + detail + POST + PUT + DELETE).
- [ ] `details()` với >100 ID (UUID cache).
- [ ] Entity có cache: hit cache vẫn đúng; `reset_cache` gọi API.

---

## Ghi chú rủi ro (so sánh với bản cũ)

1. `Notification::send` — message lỗi rỗng khi fail.
2. `Inventory\Logs` / `Transaction` — bỏ xử lý 403 đặc biệt.
3. Xóa 5 class legacy — smoke test toàn bộ màn hình từng phân hệ liên quan.
