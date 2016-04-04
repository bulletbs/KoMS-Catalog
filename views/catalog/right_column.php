<?php defined('SYSPATH') or die('No direct script access.');?>

<div class="last">
    <?php echo Widget::factory('catalogMenu')->render() ?>
    <br />
    <?php echo Widget::factory('google')->render() ?>
    <br />
    <?php echo Widget::factory('mostNews')->render() ?>
    <?php echo Widget::factory('vk')->render() ?>
</div>

