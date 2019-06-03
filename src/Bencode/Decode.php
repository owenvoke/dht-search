<?php

namespace pxgamer\DHT\Bencode;

/**
 * Class Decode.
 */
class Decode
{
    /**
     * @var string
     */
    private $source;

    /**
     * @var int
     */
    private $length;

    /**
     * @var int
     */
    private $offset = 0;

    /**
     * Decode constructor.
     * @param $source
     */
    private function __construct($source)
    {
        $this->source = $source;
        $this->length = strlen($source);
    }

    /**
     * @param mixed $source
     * @return array|bool|string
     */
    public static function decode($source)
    {
        if (! is_string($source)) {
            return '';
        }

        $decode = new self($source);
        $decoded = $decode->do_decode();

        if ($decode->offset != $decode->length) {
            return '';
        }

        return $decoded;
    }

    /**
     * @return array|bool|int|string
     */
    private function do_decode()
    {
        switch ($this->get_char()) {
            case 'i':
                ++$this->offset;

                return $this->decode_integer();
            case 'l':
                ++$this->offset;

                return $this->decode_list();
            case 'd':
                ++$this->offset;

                return $this->decode_dict();
            default:
                if (ctype_digit($this->get_char())) {
                    return $this->decode_string();
                }
        }

        return '';
    }

    /**
     * @param int|null $offset
     * @return bool
     */
    private function get_char($offset = null)
    {
        if ($offset === null) {
            $offset = $this->offset;
        }

        if (empty($this->source) || $this->offset >= $this->length) {
            return false;
        }

        return $this->source[$offset];
    }

    /**
     * @return bool|int|string
     */
    private function decode_integer()
    {
        $offset_e = strpos($this->source, 'e', $this->offset);

        if ($offset_e === false) {
            return '';
        }

        $current_off = $this->offset;

        if ($this->get_char($current_off) == '-') {
            $current_off++;
        }

        if ($offset_e === $current_off) {
            return '';
        }

        while ($current_off < $offset_e) {
            if (! ctype_digit($this->get_char($current_off))) {
                return '';
            }

            $current_off++;
        }

        $value = substr($this->source, $this->offset, $offset_e - $this->offset);
        $absolute_value = (string) abs($value);

        if (1 < strlen($absolute_value) && '0' == $value[0]) {
            return '';
        }

        $this->offset = $offset_e + 1;

        return $value + 0;
    }

    /**
     * @return array|string
     */
    private function decode_list()
    {
        $list = [];
        $terminated = false;
        $list_offset = $this->offset;

        while ($this->get_char() !== false) {
            if ($this->get_char() == 'e') {
                $terminated = true;
                break;
            }

            $list[] = $this->do_decode();
        }

        if (! $terminated && $this->get_char() === false) {
            return '';
        }

        $this->offset++;

        return $list;
    }

    /**
     * @return array|string
     */
    private function decode_dict()
    {
        $dict = [];
        $terminated = false;
        $dict_offset = $this->offset;

        while ($this->get_char() !== false) {
            if ($this->get_char() == 'e') {
                $terminated = true;
                break;
            }

            $key_offset = $this->offset;

            if (! ctype_digit($this->get_char())) {
                return '';
            }

            $key = $this->decode_string();

            if (isset($dict[$key])) {
                return '';
            }

            $dict[$key] = $this->do_decode();
        }

        if (! $terminated && $this->get_char() === false) {
            return '';
        }

        $this->offset++;

        return $dict;
    }

    /**
     * @return bool|string
     */
    private function decode_string()
    {
        if ('0' === $this->get_char() && ':' != $this->get_char($this->offset + 1)) {
            return '';
        }

        $offset_o = strpos($this->source, ':', $this->offset);

        if ($offset_o === false) {
            return '';
        }

        $content_length = (int) substr($this->source, $this->offset, $offset_o);

        if (($content_length + $offset_o + 1) > $this->length) {
            return '';
        }

        $value = substr($this->source, $offset_o + 1, $content_length);
        $this->offset = $offset_o + $content_length + 1;

        return $value;
    }
}
