<p>
    Dear <strong><?php echo $recipient; ?></strong>,
    <br/><br/>
    Thank you for your request!
    <br/>
    Your Email is : <?php echo $recipientEmail; ?>
    <br/>
    Please click on the link below to reset your password.
    <br/><br/>
    <a href='<?php echo $activationLink; ?>' target='_blank'>Reset your password</a>
    <br/><br/>
    If this is not requested by you just ignore this message.
    <br/><br/>
    <?php
    echo!empty(Yii::app()->params['copyrightInfo']) ? Yii::app()->params['copyrightInfo'] : '';
    ?>
</p>
