<?php
namespace EKulikova;

interface iResizer{

    public function resize( $new_width, $new_height );

    public function resizeToHeight( $new_height, $skip_small=1 );

    public function resizeToWidth( $new_width, $skip_small=1 );

    public function resizeToHeightWidth( $new_width, $new_height, $skip_small=1);

}
