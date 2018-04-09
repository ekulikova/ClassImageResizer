<?php
namespace EKulikova;

interface iResizer{

    public function resize($new_width,$new_height);

    public function resizeToHeight($h,$skip_small=1);

    public function resizeToWidth($w,$skip_small=1);

    public function resizeToHeightWidth($w,$h,$skip_small=1);

}
