<?php

class DatabaseCard {

    public function __construct(
        string $title, string $img_source, string $href, string $background_color, string $alt=''
    ) {
        echo '
        <!--- '. $title .' image and link--->
        <div class="col d-flex justify-content-center">
            <a href='. $href .'>
                <figure class="text-center" style="background: '. $background_color .'">
                    <img class="img-fluid img-sized" src='. $img_source .' alt='. $alt .'>
                    <figcaption>
                        <h4>'. $title .'</h4>
                    </figcaption>
                </figure>
            </a>
        </div>
        ';
    }

}