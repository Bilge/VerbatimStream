<?php
namespace Bilge\Stream;

class Verbatim {
    private
        $data,

        $ptr,

        $length
    ;

    public function stream_open($path, $mode, $options, &$opened_path) {
        //Strip 'verbatim://' prefix.
        $this->data = urldecode(substr($path, 11));
        $this->length = strlen($this->data);
        $this->ptr = 0;

        return true;
    }

    public function stream_stat() {
        return [
            'size' => $this->length
        ];
    }

    public function stream_read($count) {
        $data = substr($this->data, $this->ptr, $count);

        $this->ptr += $count;

        return $data;
    }

    public function stream_eof() {
        return $this->ptr >= $this->length;
    }
}