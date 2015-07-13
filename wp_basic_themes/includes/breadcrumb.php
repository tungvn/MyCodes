<?php
/* 
 * Display Breadcrumbs in wordpress without a plugin, be based on @http://smallseotips.com/2014/01/wordpress-seo-display-breadcrumbs-without-a-plugin/
 * If simple copy, maybe you need Font Awesome for my $delimiter (available in bootstrap 3)
 * @ https://github.com/tungvn
 */

function breadcrumbs() {
    $domain = ''; // your theme domain, for translate (with setting <translate from content>)
    /* Options */
    $text['home']     = __( 'Home', $domain );
    $text['tax']      = '%s';
    $text['search']   = __( 'Search results for', $domain ) .' "%s"';
    $text['tag']      = 'Tag "%s"';
    $text['author']   = __( 'Author', $domain ) . ' %s';
    $text['404']      = __( 'Error 404', $domain );

    $home_link        = home_url( '/' );
    $show_current     = 1; // 1 - show current post/page/category title in breadcrumbs, 0 - don't show
    $show_on_home     = 1; // 1 - show breadcrumbs on the homepage, 0 - don't show
    $show_home_link   = 1; // 1 - show the 'Home' link, 0 - don't show
    $show_title       = 1; // 1 - show the title for the links, 0 - don't show
    $delimiter        = '<span class="sub"><i class="fa fa-angle-right"></i></span>'; // delimiter between crumbs - use <fontawesome>
    $current_before   = ''; // tag before current page, custom
    $current_after    = ''; // tag after current page, custom
    $link_before      = ''; // custom
    $link_after       = ''; // custom
    $link_attr        = ' rel="v:url" property="v:title"'; // for SEO onpage
    $link             = $link_before . '<a' . $link_attr . ' href="%1$s" '. ( $show_title ? 'title="%2$s"':'' ) .'>%2$s</a>' . $link_after . $delimiter;
    $current          = $current_before . '%s' . $current_after;
    /* === End of options === */

    global $post;

    echo '<div class="breadcrumbs" rel="v:breadcrumb">';

    if( is_home() || is_front_page() ){
        if( $show_on_home )
            echo str_replace( $delimiter, '', sprintf( $link, $home_link, $text['home'] ) );
    }
    else{
        if( $show_home_link ){
            echo sprintf( $link, $home_link, $text['home'] );
        }

        if( is_404() ){
            echo sprintf( $current, $text['404'] );
        }

        elseif( is_day() ) {
            echo sprintf( $link, get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) );
            echo sprintf( $link, get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ), get_the_time( 'F' ) );
            echo sprintf( $current, get_the_time( 'd' ) );
        }

        elseif( is_month() ) {
            echo sprintf( $link, get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) );
            echo sprintf( $current, get_the_time( 'F' ) );
        }

        elseif( is_year() ) {
            echo sprintf( $current, get_the_time( 'Y' ) );
        }

        elseif( is_tax() || is_category() ){
            $term_slug = get_query_var( 'term' );
            $taxonomy = get_query_var( 'taxonomy' );
            $by = 'slug';
            if( is_category() ){
                $term_slug = get_query_var( 'cat' );
                $taxonomy = 'category';
                $by = 'id';
            }
            $current_tax = get_term_by( $by, $term_slug, $taxonomy );
            if ( $current_tax->parent != 0 ){
                $ancestors = get_ancestors( $current_tax->term_id, $taxonomy );
                $ancestors = array_reverse( $ancestors );
                foreach ($ancestors as $akey => $aterm) {
                    $term = get_term( $aterm, $taxonomy );
                    echo sprintf( $link, get_term_link( $term->slug, $taxonomy ), $term->name );
                }
            }
            if( $show_current )
                echo sprintf( $current, $current_tax->name );
        }

        elseif( is_singular() ){
            if( is_page() && !is_attachment() ){
                if( $post->post_parent != 0 ){
                    $ancestors = get_post_ancestors( $post );
                    $ancestors = array_reverse( $ancestors );
                    foreach ($ancestors as $ap) {
                        $term = get_permalink( $ap );
                        echo sprintf( $link, get_permalink( $ap ), get_the_title( $ap ) );
                    }
                }
            }
            else{
                $p = $post;
                if( is_attachment() ){
                    $p = get_post( $post->post_parent );
                }

                if( get_post_type( $p->ID ) != 'post' ){
                    $post_type = get_post_type_object( get_post_type( $p->ID ) );
                    $slug = $post_type->rewrite;
                    echo sprintf( $link, $home_link . '/' . $slug['slug'] . '/', $post_type->labels->singular_name );
                }
                else{
                    $cat = get_the_category( $p->ID ); $cat = $cat[0];
                    $cats = get_category_parents( $cat, TRUE, $delimiter );
                    if( !$show_current )
                        $cats = preg_replace( "#^(.+)$delimiter$#", "$1", $cats );
                    $cats = str_replace( '<a', $link_before . '<a' . $link_attr, $cats );
                    $cats = str_replace( '</a>', '</a>' . $link_after, $cats );
                    if( !$show_title )
                        $cats = preg_replace( '/ title="(.*?)"/', '', $cats );
                    echo $cats;

                }

                if( is_attachment() )
                    echo sprintf( $link, get_permalink( $p ), get_the_title( $p ) );
            }

            if( $show_current )
                echo sprintf( $current, get_the_title() );
        }

        elseif( is_tag() ) {
            echo sprintf( $text['tag'], single_tag_title( '', false ) );
        }

        elseif( is_author() ) {
            global $author;
            $userdata = get_userdata($author);
            echo sprintf( $text['author'], $userdata->display_name );
        }

        elseif( is_search() ){
            echo sprintf( $text['search'], get_search_query() );
        }

        if( get_query_var( 'paged' ) ) {
            if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
            echo __( 'Page' ) . ' ' . get_query_var( 'paged' );
            if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
        }
    }

    echo '</div>';
}
