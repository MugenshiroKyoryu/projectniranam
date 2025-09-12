<?php
session_start();
include_once 'config/config.php';
include_once 'config/connectdb.php';

// ถ้าไม่ได้ login ให้ redirect กลับไปหน้า index
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit;
}

$logged_in_user_id = $_SESSION['id'];

// ตรวจสอบว่ามี user_id ส่งมาจาก URL หรือไม่
if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']); // id ของผู้ขายที่กดจาก link
} else {
    $user_id = $logged_in_user_id; // default เป็นคนที่ล็อกอิน
}

// ตรวจสอบว่าอนุญาตแก้ไขหรือไม่
$can_edit = ($user_id === $logged_in_user_id);

// -------------------- อัปโหลดรูปภาพ --------------------
if ($can_edit && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];
    $uploadDir = 'uploads/';

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (in_array($file['type'], $allowedTypes) && $file['error'] === 0) {

        // ดึงข้อมูล user ก่อน
        $stmt = $conn->prepare("SELECT image FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $userData = $result->fetch_assoc();
        $stmt->close();

        // ถ้ามีรูปเก่า ให้ลบออก
        if (!empty($userData['image']) && file_exists($uploadDir . $userData['image'])) {
            unlink($uploadDir . $userData['image']);
        }

        // สร้างชื่อไฟล์ใหม่
        $filename = time() . '_' . basename($file['name']);
        $target = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            // อัปเดตฐานข้อมูล
            $stmt = $conn->prepare("UPDATE users SET image = ? WHERE user_id = ?");
            $stmt->bind_param("si", $filename, $user_id);
            $stmt->execute();
            $stmt->close();
            echo "success";
            exit;
        } else {
            echo "Upload failed!";
            exit;
        }
    } else {
        echo "Invalid file type!";
        exit;
    }
}

// -------------------- อัปเดตข้อมูลส่วนตัว --------------------
if ($can_edit && isset($_POST['type'], $_POST['value'])) {
    $type = $_POST['type'];
    $value = $_POST['value'];

    $allowed = [
        'facebook', 'twitter', 'instagram', 'github',
        'user_name', 'real_name', 'user_email', 'user_phone', 'user_status', 'description'
    ];

    if (in_array($type, $allowed)) {
        $stmt = $conn->prepare("UPDATE users SET $type = ? WHERE user_id = ?");
        $stmt->bind_param("si", $value, $user_id);
        $stmt->execute();
        $stmt->close();
        echo "success";
        exit;
    }
}

// -------------------- ดึงข้อมูล user --------------------
$sql = "SELECT user_name, real_name, user_email, user_phone, user_status, facebook, twitter, instagram, github, image, description 
        FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// ฟังก์ชันดึง username ของ social จาก URL
function getSocialUsername($url)
{
    $parts = parse_url($url);
    if (!isset($parts['host'])) return $url;

    $hosts = [
        'facebook.com' => 'facebook.com/',
        'www.facebook.com' => 'facebook.com/',
        'twitter.com' => 'twitter.com/',
        'www.twitter.com' => 'twitter.com/',
        'instagram.com' => 'instagram.com/',
        'www.instagram.com' => 'instagram.com/',
        'github.com' => 'github.com/',
        'www.github.com' => 'github.com/',
    ];

    $host = $parts['host'];
    $path = trim($parts['path'], '/');
    if (isset($hosts[$host])) {
        return $path ?: $host;
    }
    return $url;
}
?>

<!doctype html>
<html lang="en">
<head>
    <title>User Profile</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .profile-avatar {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ddd;
        }
        .list-group-item a {
            color: inherit;
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 500;
        }
        .list-group-item a:hover {
            text-decoration: underline;
            color: inherit;
        }
        body { background-color: #eeeeee; }
    </style>
</head>
<body>
<main>
    <section>
        <div class="container py-5">
            <div class="row mb-4">
                <div class="col">
                    <nav aria-label="breadcrumb" class="bg-body-tertiary rounded-3 p-3">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="home_logout_register/home.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">User Profile</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <h1 class="mb-3"><?= $can_edit ? "Your Profile" : "Seller Profile" ?></h1>

            <div class="row">
                <!-- Left Column -->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <?php
                            $avatar = $user['image'] ?? '';
                            if (!empty($avatar)) {
                                $avatarSrc = filter_var($avatar, FILTER_VALIDATE_URL) ? $avatar : 'uploads/' . $avatar;
                            } else {
                                $avatarSrc = 'https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp';
                            }
                            ?>
                            <img src="<?= htmlspecialchars($avatarSrc) ?>" alt="avatar" class="rounded-circle img-fluid profile-avatar">

                            <h5 class="my-3"><?= htmlspecialchars($user['user_name']) ?></h5>
                            <p class="text-muted mb-1"><?= htmlspecialchars($user['user_status']) ?></p>

                            <?php if($can_edit): ?>
                            <form id="uploadAvatarForm" enctype="multipart/form-data">
                                <input type="file" name="avatar" accept="image/*" class="form-control mb-2">
                                <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Social Links -->
                    <div class="card mb-4 mb-lg-0">
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush rounded-3">
                                <?php
                                $socials = [
                                    'facebook' => 'bi-facebook text-primary',
                                    'twitter' => 'bi-twitter text-info',
                                    'instagram' => 'bi-instagram text-danger',
                                    'github' => 'bi-github text-dark'
                                ];
                                foreach ($socials as $key => $icon):
                                    $value = $user[$key];
                                ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi <?= $icon ?> fs-4 me-2"></i>
                                            <?php
                                            if (!empty($value)) {
                                                if (filter_var($value, FILTER_VALIDATE_URL)) {
                                                    $username = getSocialUsername($value);
                                                    echo '<a href="' . htmlspecialchars($value) . '" target="_blank">' . htmlspecialchars($username) . '</a>';
                                                } else {
                                                    echo htmlspecialchars($value);
                                                }
                                            }
                                            ?>
                                        </div>
                                        <div>
                                            <?php if ($can_edit && !empty($user[$key])): ?>
                                                <a href="#" class="edit-field text-primary me-2" data-field="<?= $key ?>" data-value="<?= htmlspecialchars($user[$key]) ?>">✏️</a>
                                                <a href="#" class="delete-field text-danger me-2" data-field="<?= $key ?>">🗑️</a>
                                            <?php elseif($can_edit): ?>
                                                <a href="#" class="add-social text-primary me-2" data-social="<?= $key ?>">+</a>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <?php
                            $fields = [
                                'Username' => $user['user_name'],
                                'Realname' => $user['real_name'],
                                'Email' => $user['user_email'],
                                'Phone' => $user['user_phone'],
                                'Status' => $user['user_status'],
                            ];
                            foreach ($fields as $label => $value):
                                $field_key = strtolower($label);
                                $db_key = match ($field_key) {
                                    'realname' => 'real_name',
                                    'email' => 'user_email',
                                    'phone' => 'user_phone',
                                    'status' => 'user_status',
                                    'username' => 'user_name',
                                    default => ''
                                };
                            ?>
                                <div class="row">
                                    <div class="col-sm-3"><p class="mb-0"><?= $label ?></p></div>
                                    <div class="col-sm-7"><p class="text-muted mb-0"><?= htmlspecialchars($value) ?></p></div>
                                    <div class="col-sm-2 text-end">
                                        <?php if($can_edit && $db_key !== ''): ?>
                                            <a href="#" class="edit-field text-primary fw-bold" data-field="<?= $db_key ?>" data-value="<?= htmlspecialchars($value) ?>">✏️</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <hr>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="card mb-4 mb-md-0">
                            <div class="card-body">
                                <p class="mb-2">Description</p>
                                <div class="border p-3 rounded" style="min-height:100px; max-height:300px; overflow-y:auto;">
                                    <p id="userDescriptionText"><?= htmlspecialchars($user['description'] ?? 'No description available.') ?></p>
                                </div>
                                <?php if($can_edit): ?>
                                <div class="text-end mt-2">
                                    <a href="#" id="editUserDescription" class="text-primary fw-bold">✏️</a>
                                    <a href="#" id="deleteUserDescription" class="text-danger fw-bold">🗑️</a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="text-end mt-5">
    <?php if($can_edit): ?>
    <a href="home_logout_register/logout.php" class="btn btn-danger">
        <i class="bi bi-box-arrow-right"></i> Logout
    </a>
    <?php endif; ?>
</div>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="fieldType" name="fieldType">
                    <div class="mb-3">
                        <label for="fieldValue" class="form-label" id="fieldLabel">Value</label>
                        <input type="text" class="form-control" id="fieldValue" name="fieldValue" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function(){
    var editModal = new bootstrap.Modal(document.getElementById('editModal'));

    <?php if($can_edit): ?>
    // AJAX Upload Avatar
    $('#uploadAvatarForm').submit(function(e){
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: '',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(){ location.reload(); }
        });
    });

    // Add Social
    $('.add-social').click(function(e){
        e.preventDefault();
        var field = $(this).data('social');
        $('#fieldType').val(field);
        $('#fieldValue').val('');
        $('.modal-title').text("Add " + field.charAt(0).toUpperCase() + field.slice(1));
        $('#fieldLabel').text("URL / Username");
        editModal.show();
    });

    // Edit field
    $('.edit-field').click(function(e){
        e.preventDefault();
        var field = $(this).data('field');
        var value = $(this).data('value');
        $('#fieldType').val(field);
        $('#fieldValue').val(value);
        $('.modal-title').text("Edit " + field.replace('_',' '));
        $('#fieldLabel').text("New " + field.replace('_',' '));
        editModal.show();
    });

    // Submit Edit Form
    $('#editForm').submit(function(e){
        e.preventDefault();
        $.ajax({
            url:'',
            type:'POST',
            data:{ type: $('#fieldType').val(), value: $('#fieldValue').val() },
            success: function(){ location.reload(); }
        });
    });

    // Delete Social
    $('.delete-field').click(function(e){
        e.preventDefault();
        if(!confirm("คุณต้องการลบข้อมูลนี้ใช่หรือไม่?")) return;
        var field = $(this).data('field');
        $.ajax({
            url:'',
            type:'POST',
            data:{ type: field, value: '' },
            success: function(){ location.reload(); }
        });
    });

    // Edit Description
    $('#editUserDescription').click(function(e){
        e.preventDefault();
        $('#fieldType').val('description');
        $('#fieldValue').val($('#userDescriptionText').text());
        $('.modal-title').text("Edit Description");
        $('#fieldLabel').text("New Description");
        editModal.show();
    });

    // Delete Description
    $('#deleteUserDescription').click(function(e){
        e.preventDefault();
        if(!confirm("คุณต้องการลบ Description ใช่หรือไม่?")) return;
        $.ajax({
            url:'',
            type:'POST',
            data:{ type:'description', value:'' },
            success:function(){ location.reload(); }
        });
    });
    <?php endif; ?>
});
</script>
</body>
</html>

<?php $conn->close(); ?>
