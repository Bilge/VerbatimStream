<?php
namespace Bilge\Stream;

class Verbatim {
    private
        $data,

        $ptr,

        $length
    ;

    public function stream_open($path, $mode, $options, &$opened_path) {
        $this->data = $this->getPayload($path);
        $this->length = strlen($this->data);
        $this->ptr = 0;

        return true;
    }

    public function stream_stat() {
        return [
            //Read-only file.
            'mode' => 0100004,
            'size' => $this->length,
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

    public function url_stat($path, $flags) {
        $h = fopen($path, 'rb');

        try { return fstat($h); }
        finally { fclose($h); }
    }

    /**
     * Removes 'verbatim://' prefix from the specified path.
     *
     * @param $path
     * @return string Payload.
     */
    private function getPayload($path) {
        return urldecode(substr($path, 11));
    }
}