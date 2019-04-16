<?php $this->breadcrumbs = array('Dashboard'); ?>
<div class="content-panel">
    <div class="row clearfix">
        <div class="col-md-6 col-sm-6 form-group">
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Summary Of Customer, User and Others</th>
                </tr>
                <tr>
                    <td>Listed Categories <span class="pull-right"><?php echo AppObject::getCategories(); ?></span></td>
                </tr>
                <tr>
                    <td>Listed Companies <span class="pull-right"><?php echo AppObject::getCompanies(); ?></span></td>
                </tr>
                <tr>
                    <td>Listed Customers <span class="pull-right"><?php echo AppObject::getCustomers(); ?></span></td>
                </tr>
                <tr>
                    <td>Listed Products <span class="pull-right"><?php echo AppObject::getProducts(); ?></span></td>
                </tr>
                <tr>
                    <td>Listed Users <span class="pull-right"><?php echo AppObject::getUsers(); ?></span></td>
                </tr>
            </table>
        </div>
        <div class="col-md-6 col-sm-6 form-group">
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Pending Orders</th>
                </tr>
                <tr>
                    <td><?php echo $link; ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
    });
</script>