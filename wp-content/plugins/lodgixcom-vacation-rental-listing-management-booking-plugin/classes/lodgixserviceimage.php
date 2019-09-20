<?php

class LodgixServiceImage
{

    public static $BASE_URL = WP_CONTENT_URL . '/uploads/lodgix/';

    private static $VALID_TYPES = array('image/jpeg', 'image/jpg');

    private static $SIZES = array(
        array('width' => 2048, 'height' => 2048),
        array('width' => 1600, 'height' => 1600),
        array('width' => 474, 'height' => 474)
    );

    private $imgDir;

    function __construct()
    {
        $wpUploadDir = wp_upload_dir();
        $this->imgDir = trailingslashit($wpUploadDir['basedir']) . 'lodgix';
        if (!file_exists($this->imgDir)) {
            wp_mkdir_p($this->imgDir);
        }
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        WP_Filesystem();
    }

    public function downloadImage($imageName, $url, $requestTimeout = 30)
    {
        $response = wp_remote_get($url, array('timeout' => $requestTimeout));
        if (!is_wp_error($response)) {
            $contentType = wp_remote_retrieve_header($response, 'content-type');
            if (in_array($contentType, self::$VALID_TYPES)) {
                $data = wp_remote_retrieve_body($response);
                global $wp_filesystem;
                $path = "$this->imgDir/$imageName.jpg";
                $wp_filesystem->put_contents($path, $data, FS_CHMOD_FILE);
                $this->resizeImage($imageName);
                $wp_filesystem->delete($path);
            }
        }
    }

    protected function resizeImage($imageName)
    {
        foreach (self::$SIZES as $s => $size) {
            $image = wp_get_image_editor("$this->imgDir/$imageName.jpg");
            if (!is_wp_error($image)) {
                $maxW = $size['width'];
                $maxH = $size['height'];
                $image->resize($maxW, $maxH);
                $image->save("$this->imgDir/$imageName-$maxW-$maxH.jpg");
            }
        }
    }

    public function deleteAllImages()
    {
        global $wp_filesystem;
        $wp_filesystem->delete($this->imgDir, true);
    }

}
