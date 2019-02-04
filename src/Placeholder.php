<?php

namespace MTR;


class Placeholder
{
    static $width, $height, $fontSize, $fontColor, $backgroundColor;

    /**
     * returns a png
     *
     * @param int $width
     * @param int $height
     *
     * @return string
     */
    public static function png(int $width, int $height): string {
        self::init($width, $height);
        return 'data:image/png;base64,' . self::createImageResource('png');
    }

    /**
     * returns a jpg
     *
     * @param int $width
     * @param int $height
     *
     * @return string
     */
    public static function jpg(int $width, int $height): string {
        self::init($width, $height);
        return 'data:image/jpeg;base64,' . self::createImageResource('jpg');
    }


    /**
     * set the font color
     *
     * @param string $color
     *
     * @return Placeholder
     */
    public static function fontColor(string $color): Placeholder {
        self::$fontColor = $color;
        return new static;
    }

    /**
     * Set the background color
     *
     * @param string $color
     * @return Placeholder
     */
    public static function backgroundColor(string $color): Placeholder {
        self::$backgroundColor = $color;
        return new static;
    }

    /**
     * Get the font color in decimal
     *
     * @return int
     */
    private static function getFontColor(): int {
        $color = self::$fontColor ? str_replace('#', '', self::$fontColor) : 'FFFFFF';
        return hexdec($color);
    }

    /**
     * Get the backgroundcolor in decimal
     *
     * @return int
     */
    private static function getBackgroundColor(): int {
        $color = self::$backgroundColor ? str_replace('#', '', self::$backgroundColor) : '000000';
        return hexdec($color);
    }

    /**
     * Create image resource
     *
     * @param $type
     * @return string
     */
    private static function createImageResource($type): string {
        ob_start();
        $resource = imagecreatetruecolor(self::$width, self::$height);
        imagefill($resource, 0, 0, self::getBackgroundColor()); // set background color
        $textDimensions = self::getTextBoxDimensions();

        imagettftext(
            $resource,
            self::$fontSize,
            0,
            $textDimensions['x'],
            $textDimensions['y'],
            self::getFontColor(),
            self::getFontPath(),
            self::getTextString()
        );

        switch ($type) {
            case 'png':
                imagepng($resource);
                break;
            case 'jpg':
                imagejpeg($resource);
                break;
            default:
                imagejpeg($resource);
                break;
        }
        $data = ob_get_contents();
        ob_end_clean();

        self::unsetColors();

        return base64_encode($data);
    }

    /**
     * Unset the colors
     */
    private static function unsetColors(): void {
        self::$fontColor = null;
        self::$backgroundColor = null;
    }

    /**
     * Get the font path
     *
     * @return string
     */
    private static function getFontPath(): string {
        return dirname(__FILE__) . '/resources/OpenSans-Regular.ttf';
    }

    /**
     * Get dimensions of the text box
     *
     * @return array
     */
    private static function getTextBoxDimensions(): array {
        $box = imagettfbbox(self::$fontSize, 0, self::getFontPath(), self::getTextString());
        $x = $box[0] + (self::$width / 2) - ($box[4] / 2);
        $y = $box[1] + (self::$height / 2) - ($box[5] / 2);

        return ['x' => $x, 'y' => $y];
    }

    /**
     * Get the text string to place inside the image
     *
     * @return string
     */
    private static function getTextString(): string {
        return implode(' X ', [self::$width, self::$height]);
    }

    /**
     * Set the static values
     *
     * @param $width
     * @param $height
     */
    private static function init($width, $height): void {
        self::$width = $width;
        self::$height = $height;
        self::$fontSize = ($width + $height) / 20;
    }
}
