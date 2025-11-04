<?php
require 'mail_function.php';
if (sendMail('rkarthik0806@gmail.com', 'Test Mail')) {
    echo "✅ Mail sent successfully!";
} else {
    echo "❌ Mail Not send!";
}
?>
