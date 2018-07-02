<?php

class Parser
{
    const STATUS_TRUE = "ОК";
    const STATUS_FALSE = "Ошибка";
    private $site;
    private $file = 'robots.txt';
    private $requestCode = null;
    private $requestSize = null;
    private $requestLink;
    private $hostCheck = false;
    private $siteCheck = false;

    function __construct()
    {
        $this->site = $_POST['site'];
        $this->file = 'robots.txt';
        $url = $this->Adverto_SALE_CLASSIFIED_SITE($url);
        $url = $this->site . DIRECTORY_SEPARATOR . $this->file;
        $curlData = $this->curlInit($url);
        $result = $this->makeChecks($curlData);
        echo(json_encode($result));
    }

    function Adverto_SALE_CLASSIFIED_SITE($url) {
		$ret = '';
		if(empty($url) || !is_string($url) || $url == '') {
		return false;
		}
		$arUrl = parse_url($url);
		if($arUrl === FALSE) return false;

		if(empty($arUrl['host']) && empty($arUrl['scheme']) && empty($arUrl['path'])) {
		    return false;
		}
		if (empty($arUrl['scheme']) || !in_array($arUrl['scheme'], array('http', 'https'))) {
		    $arUrl['scheme'] = 'http';
		}
		if(!empty($arUrl['host']) && !empty($arUrl['scheme'])) {
		    if(!preg_match("/^([a-z0-9]{1})((\.[a-z0-9-])|([a-z0-9-]))*\.([a-z]{2,6})(\/?)$/", $arUrl['host'])) {
		        return false;
		    }
		    $ret = "{$arUrl['scheme']}://{$arUrl['host']}";
		    if(!empty($arUrl['path'])) {
		        $ret .= $arUrl['path'];
		    }
		} else if (!empty($arUrl['path']) && !empty($arUrl['scheme']) && preg_match("/^([a-z0-9]{1})((\.[a-z0-9-])|([a-z0-9-]))*\.([a-z]{2,6})(\/?)$/", $arUrl["path"])) {
		    $ret = "{$arUrl["scheme"]}://{$arUrl["path"]}";
		}
		if (!empty($arUrl["query"])) {
		    $ret .= "?{$arUrl["query"]}";
		}
		if(filter_var($ret, FILTER_VALIDATE_URL)) {
		    if( $curl = curl_init() ) {
		        curl_setopt($curl, CURLOPT_URL, $ret);
		        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		        curl_setopt($curl, CURLOPT_NOBODY, true);
		        curl_setopt($curl, CURLOPT_HEADER, true);
		        $out = curl_exec($curl);
		        curl_close($curl);
		    }
		    if($out === FALSE) {
		        return false;
		    }
		    $status = explode("\r\n", $out);
		    if(in_array("HTTP/1.1 200 OK", $status) || in_array("HTTP/1.0 200 OK", $status)) {
		        return $ret;
		    } else if(in_array("HTTP/1.1 301 Moved Permanently", $status) || in_array("HTTP/1.0 301 Moved Permanently", $status)) {
		        foreach ($status as  $key=>$val) {
		            if (strpos($val, 'Location: ') !== false) {
		                return str_replace('Location: ', '', $status[$key]);
		            }
		        }
		    }
		}
		return false;
	}

    private function curlInit($url)
    {
        $this->requestLink = $url;
        $curl_init = curl_init();
        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_HEADER         => true
        ];
        curl_setopt_array($curl_init, $options);
        $result = curl_exec($curl_init);
        if ($result !== false) {
            $this->requestCode = curl_getinfo($curl_init, CURLINFO_HTTP_CODE);
            $this->requestSize = curl_getinfo($curl_init, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
            if ($this->requestCode > 300 && $this->requestCode < 400) {
                preg_match('/Location:(.*?)\n/', $result, $matches);
                if (is_array($matches) && isset($matches[1])) {
                    $url = trim($matches[1]);
                    $this->requestLink = $url;
                    $this->curlInit($url);
                }
            }
        }

        return $result;
    }

    private function makeChecks($data)
    {
        $checks[] = $this->isIssetFileCheck();
        $checks[] = $this->codServerCheck();
        if ($this->siteCheck) {
            $checks[] = $this->sizeFileCheck();
            $checks[] = $this->isIssetHostCheck($data);
            if ($this->hostCheck) {
                $checks[] = $this->hostCountCheck($data);
            }
            $checks[] = $this->siteMapCheck($data);
        }

        return $checks;
    }

    private function isIssetFileCheck()
    {
        $name = 'Проверка наличия файла robots.txt';
        $text = [
            'status'  => 'Файл robots.txt отсутствует',
            'comment' => 'Программист: Создать файл robots.txt и разместить его на сайте.'
        ];
        $status = false;
        if (!is_null($this->requestCode) && $this->requestCode == 200) {
            $status = !$status;
            $this->siteCheck = $status;
            $text = [
                'status'  => 'Файл robots.txt присутствует',
                'comment' => 'Доработки не требуются'
            ];
        }
        if ($status) {
            $status = self::STATUS_TRUE;
        } else {
            $status = self::STATUS_FALSE;
        }
        return [
        	'id'	 => '1',
            'link'   => $this->requestLink,
            'name'   => $name,
            'text'   => $text,
            'status' => $status
        ];
    }

    private function isIssetHostCheck($data)
    {
        $name = 'Проверка указания директивы Host';
        $text = [
            'status'  => 'В файле robots.txt не указана директива Host',
            'comment' => 'Программист: Для того, чтобы поисковые системы знали, какая версия сайта является основных зеркалом, необходимо прописать адрес основного зеркала в директиве Host. В данный момент это не прописано. Необходимо добавить в файл robots.txt директиву Host. Директива Host задётся в файле 1 раз, после всех правил.'
        ];
        $status = false;
        if (preg_match('/Host:(.*?)\n/', $data)) {
            $status = !$status;
            $text = [
                'status'  => 'Директива Host указана',
                'comment' => 'Доработки не требуются'
            ];
        }
        $this->hostCheck = $status;
        if ($status) {
            $status = self::STATUS_TRUE;
        } else {
            $status = self::STATUS_FALSE;
        }
        return [
        	'id'	 => '2',
            'link'   => $this->requestLink,
            'name'   => $name,
            'text'   => $text,
            'status' => $status
        ];
    }

    private function hostCountCheck($data)
    {
        $name = 'Проверка количества директив Host, прописанных в файле';
        $text = [
            'status'  => 'В файле прописано несколько директив Host',
            'comment' => 'Программист: Директива Host должна быть указана в файле толоко 1 раз. Необходимо удалить все дополнительные директивы Host и оставить только 1, корректную и соответствующую основному зеркалу сайта'
        ];
        $status = false;
        if (substr_count($data, 'Host:') == 1) {
            $status = !$status;
            $text = [
                'status'  => 'В файле прописана 1 директива Host',
                'comment' => 'Доработки не требуются'
            ];
        }
        if ($status) {
            $status = self::STATUS_TRUE;
        } else {
            $status = self::STATUS_FALSE;
        }
        return [
        	'id'	 => '3',
            'link'   => $this->requestLink,
            'name'   => $name,
            'text'   => $text,
            'status' => $status
        ];
    }

    private function sizeFileCheck()
    {
        $name = 'Проверка размера файла robots.txt';
        if (($this->requestSize / 1024) >= 32) {
            $text = [
                'status'  => sprintf('Размер файла robots.txt составляет %d байт, что превышает допустимую норму', $this->requestSize),
                'comment' => 'Программист: Максимально допустимый размер файла robots.txt составляем 32 кб. Необходимо отредактировть файл robots.txt таким образом, чтобы его размер не превышал 32 Кб'
            ];
            $status = false;
        } else {
            $status = true;
            $text = [
                'status'  => sprintf('Размер файла robots.txt составляет %d байт, что находится в пределах допустимой нормы', $this->requestSize),
                'comment' => 'Доработки не требуются'
            ];
        }
        if ($status) {
            $status = self::STATUS_TRUE;
        } else {
            $status = self::STATUS_FALSE;
        }
        return [
        	'id'	 => '4',
            'link'   => $this->requestLink,
            'name'   => $name,
            'text'   => $text,
            'status' => $status
        ];
    }

    private function siteMapCheck($data)
    {
        $name = 'Проверка указания директивы Sitemap';
        $text = [
            'status'  => 'В файле robots.txt не указана директива Sitemap',
            'comment' => 'Программист: Добавить в файл robots.txt директиву Sitemap.'
        ];
        $status = false;
        if (preg_match('/Sitemap:(.*?)\n/', $data)) {
            $status = !$status;
            $text = [
                'status'  => 'Директива Sitemap указана',
                'comment' => 'Доработки не требуются'
            ];
        }
        $this->hostCheck = $status;
        if ($status) {
            $status = self::STATUS_TRUE;
        } else {
            $status = self::STATUS_FALSE;
        }
        return [
        	'id'	 => '5',
            'link'   => $this->requestLink,
            'name'   => $name,
            'text'   => $text,
            'status' => $status
        ];
    }

    private function codServerCheck()
    {
        $name = 'Проверка кода ответа сервера для файла robots.txt';
        if ($this->siteCheck) {
            $text = [
                'status'  => 'Файл robots.txt отдаёт код ответа сервера 200',
                'comment' => 'Доработки не требуются'
            ];
            $status = true;
        } else {
            $status = false;
            $text = [
                'status'  => sprintf('При обращении к файлу robots.txt сервер возвращает код ответа %d', $this->requestCode),
                'comment' => 'Программист: Файл robots.txt должны отдавать код ответа 200, иначе файл не будет обрабатываться. Необходимо настроить сайт таким образом, чтобы при обращении к файлу sitemap.xml сервер возвращал код ответа 200'
            ];
        }
        if ($status) {
            $status = self::STATUS_TRUE;
        } else {
            $status = self::STATUS_FALSE;
        }
        return [
        	'id'	 => '6',
            'link'   => $this->requestLink,
            'name'   => $name,
            'text'   => $text,
            'status' => $status
        ];
    }
}

new Parser();

