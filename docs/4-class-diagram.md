# 4. Biểu đồ lớp tổng quát (cập nhật theo phiên bản mới nhất của repo)

Tài liệu này cập nhật Mục 4 để khớp chính xác với các model và migration hiện có trong repository (phiên bản mới nhất). Nội dung tập trung vào các lớp thực tế, thuộc tính chính (theo migration), phương thức đề xuất và quan hệ.

## 4.1. Lớp chính (dựa trên code hiện tại)
- User
- Tree
- TreeCategory
- Location
- WorkOrder (đề xuất)
- Inspection (đề xuất)
- Attachment (polymorphic)
- Team (đề xuất)

## 4.2. Thuộc tính chi tiết (đối chiếu với migrations hiện có)

### 4.2.1 User
- id: int
- name: string
- email: string
- role: string (migration 2025_01_01_000001_add_role_to_users_table.php thêm cột role với default 'student')
- phone: string|null (nếu thêm)
- avatar_url: string|null (nếu thêm)
- created_at, updated_at

### 4.2.2 Tree
(Đối chiếu: database/migrations/2025_08_15_040355_create_trees_table.php và database/migrations/2025_10_27_125000_modify_trees_columns.php)
- id: int
- name: string
- scientific_name: string|null
- category_id: int -> tree_categories.id
- location_id: int -> locations.id
- planting_date (planting_date): date|null
- height: decimal(5,2)|float|null
- diameter: decimal(5,2)|float|null
- health_status: string
  - Tạo ban đầu là enum('excellent','good','fair','poor') default 'good' (theo create_trees migration)
  - Migration modify_trees_columns thay đổi health_status thành string(50) nullable (để linh hoạt hơn)
- image_url: text|null (đã change() sang text theo migration)
- notes: text|null (đã change() sang text theo migration)
- created_by: int|null
- created_at, updated_at, deleted_at (soft deletes khuyến cáo)

### 4.2.3 TreeCategory
(Theo database/migrations/2025_08_15_040340_create_tree_categories_table.php)
- id: int
- name: string
- description: text|null
- care_frequency_days: int default 30
- created_at, updated_at

### 4.2.4 Location
(Theo database/migrations/2025_08_15_040341_create_locations_table.php)
- id: int
- name: string
- description: text|null
- coordinates: string|null (lưu 'lat,lng' hoặc GeoJSON)
- area_size: decimal(8,2)|null
- created_at, updated_at

### 4.2.5 Các migration khác liên quan
- Có migration thêm/modify dành cho users, cache, jobs, sessions, và một số migration system-default (jobs, failed_jobs).
- Lưu ý: migration add_role_to_users_table dùng default 'student' (cần đổi nếu muốn role khác).

## 4.3. Các lớp đề xuất bổ sung (WorkOrder, Inspection, Attachment, Team)
(Những lớp này chưa có migrations trong repo — mình đề xuất các trường như sau để bổ sung chức năng quản lý công việc/kiểm tra):

WorkOrder:
- id, tree_id, type, status, assigned_user_id, assigned_team_id, scheduled_at, started_at, completed_at, result_notes, created_by, timestamps, deleted_at

Inspection:
- id, tree_id, inspector_id, inspected_at, issues_found, severity, recommended_action, photo_paths(json), timestamps

Attachment (polymorphic):
- id, attachable_id, attachable_type, path, mime_type, timestamps

Team:
- id, name, note, timestamps

## 4.4. Phương thức chính (gợi ý thực thi trong Model/Service)
- Tree: viewDetails(), updateAttributes(), markAsRemoved(), scheduleMaintenance(), recordInspection(), getHistory()
- WorkOrder: assignTo(), start(), complete(), cancel(), notifyAssignee()
- Inspection: submitReport(), attachPhotos(), markSeverity()
- User: isAdmin(), isInspector(), isWorker()
- Attachment: upload(), url()

## 4.5. Quan hệ chính (ER)
- User 1 — N Tree (created_by)
- Tree N — 1 TreeCategory
- Location 1 — N Tree
- Tree 1 — N WorkOrder
- Tree 1 — N Inspection
- Inspection/Tree 1 — N Attachment (polymorphic)

## 4.6. Ghi chú về trạng thái và dữ liệu đã thay đổi
- health_status ban đầu là enum trong migration tạo bảng cây nhưng sau đó được migration sửa sang string(50) nullable. Tài liệu đã cập nhật để phản ánh thay đổi.
- image_url và notes đã được migrate sang text (migration 2025_10_27_125000_modify_trees_columns.php sử dụng change()).

## 4.7. Bước tiếp theo mình có thể làm
- Tạo migrations + models skeleton cho WorkOrder, Inspection, Attachment, Team và open PR.
- Thêm soft deletes cho Tree (nếu bạn đồng ý) và cập nhật model Tree.php.
- Hoặc chỉ commit file docs/4-class-diagram.md vào nhánh hiện tại.

---

(Cuối file)