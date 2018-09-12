<section role="legend">
    <h3>Access to...</h3>
    <ul>
        <?php $legende = getFolderLegend(); ?>
        <?php foreach ($legende as $color => $texte) : ?>
            <li><span class="icon-folder-open" data-color="<?= $color; ?>"></span> <?= $texte; ?></li>
        <?php endforeach; ?>
    </ul>
</section>