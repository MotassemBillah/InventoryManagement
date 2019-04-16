<p>
    Dear <strong><?php echo $recipient; ?></strong>,
    <br/><br/>
    Thank you for joining.
    <br/>
    Please click on the link below to confirm your registration.
    <br/><br/>
    Please <a href="<?php echo $activationLink; ?>">Confirm Registration</a>
    <br/><br/>
    Your login credential is:<br/>
    <strong>Username : </strong><?php echo $recipient; ?><br/>
    <strong>Password : </strong><?php echo $recipientPass; ?>
    <br/><br/>
    <?php
    echo!empty(Yii::app()->params['copyrightInfo']) ? Yii::app()->params['copyrightInfo'] . ' All Right Reserved.' : '';
    ?>
</p>
