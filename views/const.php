<script>
    <?php foreach ($_CONFIG as $key => $value): ?>
    const <?=$key ?> = '<?=$value ?>';
    <?php endforeach; ?>
</script>