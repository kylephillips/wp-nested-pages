<?php
namespace NestedPages\Activation;

class Dev
{
    public function __construct()
    {
        add_action('admin_footer', [$this, 'livereload']);
    }

    public function livereload()
    {
        if ( !defined( 'WP_DEBUG' ) || !WP_DEBUG ) return;
        ?>
        <script>
        (function () {
            var sheets = Array.from( document.querySelectorAll( 'link[rel=stylesheet]' ) ).filter( function ( el ) { return el.href.indexOf( location.origin ) === 0; } );
            var mods   = {};
            setInterval( function () {
                sheets.forEach( function ( sheet ) {
                    var base = sheet.href.split( '?' )[0];
                    var xhr  = new XMLHttpRequest();
                    xhr.open( 'HEAD', base + '?_check=' + Date.now(), true );
                    xhr.onload = function () {
                        var lm = xhr.getResponseHeader( 'last-modified' );
                        if ( mods[base] !== undefined && lm !== mods[base] ) {
                            sheet.href = base + '?_lr=' + Date.now();
                        }
                        mods[base] = lm;
                    };
                    xhr.send();
                } );
            }, 1000 );
        })();
        </script>
        <?php
    }
}