<?php defined('SYSPATH') or die('No direct script access.');?>
<html>
<head>
    <title></title>
</head>
<body>
<b>Здравствуйте!</b><br />
<br />
Вам отправили соообщение со страницы каталога компаний.<br />
==========================================<br />
<b>Контактный e-mail: </b><a href="mailto:<?php echo $email?>"><?php echo $email?></a><br />
<b>Текст сообщения: </b><br /><?php echo nl2br($text)?></a><br />
<br />
==========================================<br />
С уважением,<br />
Администрация сайта <?php echo $site_name ?>
</body>
</html>