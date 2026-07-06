<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_login();

$pageTitle = 'My Profile';
require_once __DIR__ . '/../../layout/header.php';

$userId = (int) current_user()['id'];
$stmt = db()->prepare('SELECT u.*, r.name AS role_name, p.address, p.bank_name, p.bank_account, p.ifsc, p.emergency_name, p.emergency_mobile FROM users u LEFT JOIN roles r ON r.id = u.role_id LEFT JOIN user_profiles p ON p.user_id = u.id WHERE u.id = ? LIMIT 1');
$stmt->execute([$userId]);
$profile = $stmt->fetch() ?: [];
$completion = profile_completion($profile, $profile);
$timeouts = [15, 30, 45, 60, 90, 120, 240];
$languages = ['en' => 'English', 'hi' => 'Hindi'];
?>
<?php require __DIR__ . '/../../layout/sidebar.php'; ?>
<div class="main-panel">
<?php require __DIR__ . '/../../layout/topbar.php'; ?>
<main class="content">
    <?php require __DIR__ . '/../../layout/breadcrumbs.php'; ?>
    <div class="profile-layout">
        <aside class="panel profile-summary">
            <div class="profile-photo-wrap">
                <?php if (!empty($profile['photo'])): ?>
                    <img src="<?= e(upload_url($profile['photo'])) ?>" alt="Profile photo">
                <?php else: ?>
                    <div class="profile-avatar large"><?= e(user_initials($profile['name'] ?? 'User')) ?></div>
                <?php endif; ?>
            </div>
            <h2><?= e($profile['name'] ?? 'User') ?></h2>
            <p><?= e($profile['role_name'] ?? 'Staff') ?></p>
            <div class="completion-row">
                <span>Profile Completion</span>
                <strong><?= e((string) $completion) ?>%</strong>
            </div>
            <div class="progress" role="progressbar" aria-label="Profile completion">
                <div class="progress-bar" style="width: <?= e((string) $completion) ?>%"></div>
            </div>
            <div class="summary-list">
                <span><i class="fa-solid fa-envelope"></i><?= e($profile['email'] ?? 'Not set') ?></span>
                <span><i class="fa-solid fa-phone"></i><?= e($profile['mobile'] ?? 'Not set') ?></span>
                <span><i class="fa-solid fa-shield-halved"></i><?= e($profile['status'] ?? 'active') ?></span>
            </div>
        </aside>

        <section class="panel">
            <div class="panel-header">
                <h2>Account Details</h2>
                <span class="badge text-bg-light">Staff Profile</span>
            </div>
            <form method="post" action="<?= e(app_url('modules/profile/update.php')) ?>" class="row g-3">
                <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>">
                <div class="col-md-6 form-floating">
                    <input class="form-control" id="name" name="name" value="<?= e($profile['name'] ?? '') ?>" required maxlength="120">
                    <label for="name">Full Name</label>
                </div>
                <div class="col-md-6 form-floating">
                    <input class="form-control" id="email" name="email" type="email" value="<?= e($profile['email'] ?? '') ?>" maxlength="120">
                    <label for="email">Email</label>
                </div>
                <div class="col-md-6 form-floating">
                    <input class="form-control" id="mobile" name="mobile" value="<?= e($profile['mobile'] ?? '') ?>" maxlength="30">
                    <label for="mobile">Mobile</label>
                </div>
                <div class="col-md-6 form-floating">
                    <select class="form-select" id="language" name="language">
                        <?php foreach ($languages as $code => $label): ?>
                            <option value="<?= e($code) ?>" <?= selected_attr($profile['language'] ?? 'en', $code) ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="language">Language</label>
                </div>
                <div class="col-12 form-floating">
                    <textarea class="form-control textarea-lg" id="address" name="address" maxlength="500"><?= e($profile['address'] ?? '') ?></textarea>
                    <label for="address">Address</label>
                </div>

                <div class="col-12"><h3 class="form-section-title">Bank Details</h3></div>
                <div class="col-md-4 form-floating">
                    <input class="form-control" id="bank_name" name="bank_name" value="<?= e($profile['bank_name'] ?? '') ?>" maxlength="120">
                    <label for="bank_name">Bank Name</label>
                </div>
                <div class="col-md-4 form-floating">
                    <input class="form-control" id="bank_account" name="bank_account" value="<?= e($profile['bank_account'] ?? '') ?>" maxlength="60">
                    <label for="bank_account">Account Number</label>
                </div>
                <div class="col-md-4 form-floating">
                    <input class="form-control" id="ifsc" name="ifsc" value="<?= e($profile['ifsc'] ?? '') ?>" maxlength="20">
                    <label for="ifsc">IFSC</label>
                </div>

                <div class="col-12"><h3 class="form-section-title">Emergency Contact</h3></div>
                <div class="col-md-6 form-floating">
                    <input class="form-control" id="emergency_name" name="emergency_name" value="<?= e($profile['emergency_name'] ?? '') ?>" maxlength="120">
                    <label for="emergency_name">Contact Name</label>
                </div>
                <div class="col-md-6 form-floating">
                    <input class="form-control" id="emergency_mobile" name="emergency_mobile" value="<?= e($profile['emergency_mobile'] ?? '') ?>" maxlength="30">
                    <label for="emergency_mobile">Contact Mobile</label>
                </div>

                <div class="col-12"><h3 class="form-section-title">Preferences</h3></div>
                <div class="col-md-4 form-floating">
                    <select class="form-select" id="theme" name="theme">
                        <?php foreach (available_themes() as $theme): ?>
                            <option value="<?= e($theme) ?>" <?= selected_attr($profile['theme'] ?? 'blue', $theme) ?>><?= e(ucwords(str_replace('-', ' ', $theme))) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="theme">Theme</label>
                </div>
                <div class="col-md-4 form-floating">
                    <select class="form-select" id="mode" name="mode">
                        <option value="light" <?= selected_attr($profile['mode'] ?? 'light', 'light') ?>>Light</option>
                        <option value="dark" <?= selected_attr($profile['mode'] ?? 'light', 'dark') ?>>Dark</option>
                    </select>
                    <label for="mode">Mode</label>
                </div>
                <div class="col-md-4 form-floating">
                    <select class="form-select" id="session_timeout" name="session_timeout">
                        <?php foreach ($timeouts as $timeout): ?>
                            <option value="<?= e((string) $timeout) ?>" <?= ((int) ($profile['session_timeout'] ?? 15) === $timeout) ? 'selected' : '' ?>><?= e((string) $timeout) ?> minutes</option>
                        <?php endforeach; ?>
                    </select>
                    <label for="session_timeout">Session Timeout</label>
                </div>
                <div class="col-12 text-end">
                    <button class="btn btn-gradient" type="submit"><i class="fa-solid fa-floppy-disk me-2"></i>Save Profile</button>
                </div>
            </form>
        </section>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-xl-7">
            <section class="panel">
                <div class="panel-header"><h2>Photo & Signature</h2></div>
                <form method="post" action="<?= e(app_url('modules/profile/upload.php')) ?>" enctype="multipart/form-data" class="upload-grid">
                    <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>">
                    <label class="upload-box">
                        <input type="file" name="photo" accept="image/png,image/jpeg,image/webp">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <strong>Profile Photo</strong>
                        <span>PNG, JPG or WEBP up to 2 MB</span>
                    </label>
                    <label class="upload-box">
                        <input type="file" name="signature" accept="image/png,image/jpeg,image/webp">
                        <i class="fa-solid fa-signature"></i>
                        <strong>Signature</strong>
                        <span>Transparent PNG preferred</span>
                    </label>
                    <div class="upload-preview">
                        <div>
                            <span>Current Photo</span>
                            <?php if (!empty($profile['photo'])): ?>
                                <img src="<?= e(upload_url($profile['photo'])) ?>" alt="Current photo">
                            <?php else: ?>
                                <small>Not uploaded</small>
                            <?php endif; ?>
                        </div>
                        <div>
                            <span>Current Signature</span>
                            <?php if (!empty($profile['signature'])): ?>
                                <img src="<?= e(upload_url($profile['signature'])) ?>" alt="Current signature">
                            <?php else: ?>
                                <small>Not uploaded</small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button class="btn btn-outline-primary" type="submit"><i class="fa-solid fa-upload me-2"></i>Upload Files</button>
                </form>
            </section>
        </div>
        <div class="col-xl-5">
            <section class="panel">
                <div class="panel-header"><h2>Change Password</h2></div>
                <form method="post" action="<?= e(app_url('modules/profile/password.php')) ?>" class="row g-3">
                    <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>">
                    <div class="col-12 form-floating">
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                        <label for="current_password">Current Password</label>
                    </div>
                    <div class="col-12 form-floating">
                        <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                        <label for="new_password">New Password</label>
                    </div>
                    <div class="col-12 form-floating">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8">
                        <label for="confirm_password">Confirm Password</label>
                    </div>
                    <div class="col-12 text-end">
                        <button class="btn btn-gradient" type="submit"><i class="fa-solid fa-key me-2"></i>Update Password</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../../layout/footer.php'; ?>
