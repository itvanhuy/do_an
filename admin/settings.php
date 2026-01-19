<?php
// File: admin/settings.php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/database.php';

Auth::requireAdmin();

$db = Database::getInstance();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Trong thực tế, bạn sẽ lưu cài đặt vào database
        $message = '<div class="alert success">Settings saved successfully!</div>';
    } catch (PDOException $e) {
        $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-header">
                <h2>Settings</h2>
            </div>
            
            <?php echo $message; ?>
            
            <!-- Settings Tabs -->
            <div class="settings-tabs">
                <button class="tab-btn active" data-tab="general">General</button>
                <button class="tab-btn" data-tab="store">Store Settings</button>
                <button class="tab-btn" data-tab="payment">Payment</button>
                <button class="tab-btn" data-tab="email">Email</button>
                <button class="tab-btn" data-tab="seo">SEO</button>
                <button class="tab-btn" data-tab="security">Security</button>
            </div>
            
            <!-- Settings Forms -->
            <form method="POST">
                <!-- General Settings -->
                <div class="tab-content active" id="general">
                    <div class="settings-section">
                        <h3>General Information</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="site_name">Site Name</label>
                                <input type="text" id="site_name" name="site_name" 
                                       value="<?php echo SITE_NAME; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="site_url">Site URL</label>
                                <input type="url" id="site_url" name="site_url" 
                                       value="<?php echo SITE_URL; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="admin_email">Admin Email</label>
                                <input type="email" id="admin_email" name="admin_email" 
                                       value="admin@techshop.com">
                            </div>
                            
                            <div class="form-group">
                                <label for="timezone">Timezone</label>
                                <select id="timezone" name="timezone">
                                    <option value="Asia/Ho_Chi_Minh" selected>Asia/Ho_Chi_Minh</option>
                                    <option value="UTC">UTC</option>
                                    <!-- Add more timezones -->
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h3>Store Information</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="store_phone">Store Phone</label>
                                <input type="text" id="store_phone" name="store_phone" 
                                       value="(+84) 0896 492 400">
                            </div>
                            
                            <div class="form-group">
                                <label for="store_email">Store Email</label>
                                <input type="email" id="store_email" name="store_email" 
                                       value="levanhuy06042003@gmail.com">
                            </div>
                            
                            <div class="form-group full-width">
                                <label for="store_address">Store Address</label>
                                <textarea id="store_address" name="store_address" rows="3">99 To Hien Thanh, Son Tra, Da Nang</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Store Settings -->
                <div class="tab-content" id="store">
                    <div class="settings-section">
                        <h3>Store Configuration</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="currency">Currency</label>
                                <select id="currency" name="currency">
                                    <option value="USD" selected>USD ($)</option>
                                    <option value="VND">VND (₫)</option>
                                    <option value="EUR">EUR (€)</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="tax_rate">Tax Rate (%)</label>
                                <input type="number" id="tax_rate" name="tax_rate" 
                                       value="10" step="0.1">
                            </div>
                            
                            <div class="form-group">
                                <label for="shipping_cost">Default Shipping Cost</label>
                                <input type="number" id="shipping_cost" name="shipping_cost" 
                                       value="5.00" step="0.01">
                            </div>
                            
                            <div class="form-group">
                                <label for="min_order">Minimum Order Amount</label>
                                <input type="number" id="min_order" name="min_order" 
                                       value="0" step="0.01">
                            </div>
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h3>Inventory Settings</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="low_stock_notify" checked>
                                    <span>Notify when stock is low</span>
                                </label>
                            </div>
                            
                            <div class="form-group">
                                <label for="low_stock_threshold">Low Stock Threshold</label>
                                <input type="number" id="low_stock_threshold" name="low_stock_threshold" 
                                       value="10">
                            </div>
                            
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="allow_backorders" checked>
                                    <span>Allow backorders</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Settings -->
                <div class="tab-content" id="payment">
                    <div class="settings-section">
                        <h3>Payment Methods</h3>
                        <div class="payment-methods">
                            <div class="payment-method">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="paypal_enabled" checked>
                                    <span>PayPal</span>
                                </label>
                                <div class="payment-details">
                                    <input type="text" name="paypal_email" 
                                           placeholder="PayPal Email" 
                                           value="paypal@techshop.com">
                                </div>
                            </div>
                            
                            <div class="payment-method">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="stripe_enabled">
                                    <span>Stripe</span>
                                </label>
                                <div class="payment-details">
                                    <input type="text" name="stripe_key" 
                                           placeholder="Publishable Key">
                                    <input type="text" name="stripe_secret" 
                                           placeholder="Secret Key">
                                </div>
                            </div>
                            
                            <div class="payment-method">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="cod_enabled" checked>
                                    <span>Cash on Delivery</span>
                                </label>
                            </div>
                            
                            <div class="payment-method">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="bank_transfer_enabled" checked>
                                    <span>Bank Transfer</span>
                                </label>
                                <div class="payment-details">
                                    <textarea name="bank_details" rows="3" 
                                              placeholder="Bank account details"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Save Button -->
                <div class="settings-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save All Settings
                    </button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all tabs
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            // Add active class to current tab
            this.classList.add('active');
            const tabId = this.dataset.tab;
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Toggle payment method details
    document.querySelectorAll('.payment-method input[type="checkbox"]').forEach(cb => {
        cb.addEventListener('change', function() {
            const details = this.closest('.payment-method').querySelector('.payment-details');
            if (details) {
                details.style.display = this.checked ? 'block' : 'none';
            }
        });
        
        // Trigger change event on load
        cb.dispatchEvent(new Event('change'));
    });
    </script>
</body>
</html>