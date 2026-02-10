<?php include 'header.php'; ?>

<!-- Content Wrapper START -->
<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Payment Methods</h4>
                    </div>
                    <div class="card-body">
                        <form id="paymentMethodForm">
                            <div class="form-group">
                                <label>Select Default Payment Method</label>
                                <select class="form-control" name="payment_method">
                                    <?php
                                    $methods = ['Bank Transfer', 'PayPal', 'Bitcoin', 'Ethereum', 'Credit Card'];
                                    foreach ($methods as $method) {
                                        $selected = ($user['payment_method'] == $method) ? 'selected' : '';
                                        echo '<option value="'.$method.'" '.$selected.'>'.$method.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Content Wrapper END -->

<?php include 'footer.php'; ?>