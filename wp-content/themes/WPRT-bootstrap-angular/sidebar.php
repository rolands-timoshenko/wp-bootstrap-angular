<div class="col-sm-4">
    <div id="sidebar">
    <?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('Sidebar Widgets')) : else : ?>
        <?php get_search_form(); ?>
        <?php wp_list_pages('title_li=<h2>Pages</h2>' ); ?>
        <h2>Archives</h2>
        <ul><?php wp_get_archives('type=monthly'); ?></ul>
        <h2>Categories</h2>
        <ul><?php wp_list_categories('show_count=1&title_li='); ?></ul>
        <?php wp_list_bookmarks(); ?>
        <?php endif; ?>
    </div>
</div>