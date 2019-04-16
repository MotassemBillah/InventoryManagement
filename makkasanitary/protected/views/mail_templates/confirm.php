<p>
    Dear <strong><?php echo $recipient; ?></strong>,
    <br/><br/>
    Your Password has been changed Successfully!
    <br/><br/>
    If this is not requested by you just ignore this message.
    <br/><br/>
    <?php echo!empty(Yii::app()->params['copyrightInfo']) ? Yii::app()->params['copyrightInfo'] : ''; ?>
</p>
