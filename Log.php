<?php
/**
 * 日志类(暂不支持中文目录)
 * @name    Log
 * @date    2018-06-22
 * @author  605590351@qq.com
 */
class Log {

    private $type = "File";
    private $size = 2147483648; // 默认日志大小2Gb

    private $validTypeArr = ['File'];
    private $maxSize = 2147483648; // 日志大小上限2Gb

    private $warningP = 0.95;

    public function __construct() {
        $this->warningLimit = (int)($this->size * $this->warningP);
    }

    public function setSize($value) {
        $value = (int)$value;
        if ($value > $this->maxSize) {
            throw new Exception("Error: Size large more than maxsize");
        } else {
            $this->size = $value;
            $this->warningLimit = (int)($this->size * $this->warningP);
        }
    }

    public function setType($value) {
        if (in_array($value, $this->validTypeArr)) {
            $this->type = $value;
        } else {
            throw new Exception("Error: Type is not valid");
        }
    }

    public function getSize() {
        return $this->size;
    }

    public function getType() {
        return $this->type;
    }

    public function getMaxSize() {
        return $this->maxSize;
    }

    public function getValidTypeArr() {
        return $this->validTypeArr;
    }

    public function getDate() {
        return date("Y-m-d H:i:s");
    }

    public function handleData($value) {
        if (is_array($value)
            || is_object($value)) {
            $value = json_encode($value);
        }
        return $value;
    }

    public function handleDir($value) {
        $value = str_replace(["\\", "\\\\"], "/", $value);
        $lastPos = strripos($value, "/");
        $dir = substr($value, 0, $lastPos);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir.substr($value, $lastPos);
    }

    public function write($filename, $data, $type = "Log", $append = true) {
        $date = $this->getDate();
        $filename = $this->handleDir($filename);
        $type = strtoupper($type);
        $data = "[{$date}][{$type}]:".$this->handleData($data).PHP_EOL;
        if (is_file($filename)) {
            if (filesize($filename) >= $this->warningLimit
                && filesize($filename) < $this->size) {
                ($append) ?
                    file_put_contents($filename, $data, FILE_APPEND) :
                    file_put_contents($filename, $data);
                return "Warning: File close to MaxSize, Last less than 5%";
            } elseif (filesize($filename) >= $this->size) {
                throw new Exception("Error: File large than MaxSize");
            } else {
                ($append) ?
                    file_put_contents($filename, $data, FILE_APPEND) :
                    file_put_contents($filename, $data);
            }
        } else {
            ($append) ?
                file_put_contents($filename, $data, FILE_APPEND) :
                file_put_contents($filename, $data);
        }
    }

    public static function descAll($filename) {
        if (!is_file($filename)) {
            throw new Exception("Error: File is not exist");
        }
        $file = fopen($filename, "r");
        $content = [];
        while (!feof($file)) {
            $temp = fgets($file);
            if ($temp == "") continue;
            $content[] = $temp;
        }
        return $content;
    }

    public static function descByTime($filename, $start, $end) {
        if (!is_file($filename)) {
            throw new Exception("Error: File is not exist");
        }
        $file = fopen($filename, "r");
        $content = [];
        $start = strtotime($start);
        $end = strtotime($end);
        while (!feof($file)) {
            $temp = fgets($file);
            if ($temp == "") continue;
            $time = strtotime(substr(explode("]", $temp)[0], 1));
            if ($time >= $start && $time <= $end) {
                $content[] = $temp;
            }
            $content[] = $temp;
        }
        return $content;
    }

    public static function descByTimeAndType($filename, $start, $end, $type) {
        if (!is_file($filename)) {
            throw new Exception("Error: File is not exist");
        }
        $file = fopen($filename, "r");
        $content = [];
        $start = strtotime($start);
        $end = strtotime($end);
        $type = strtoupper($type);
        while (!feof($file)) {
            $temp = fgets($file);
            if ($temp == "") continue;
            $tempArr = explode("]", $temp);
            $time = strtotime(substr($tempArr[0], 1));
            $tempType = substr($tempArr[1], 1);
            if ($time >= $start
                && $time <= $end
                && $type == $tempType) {
                $content[] = $temp;
            }
            $content[] = $temp;
        }
        return $content;
    }
}