<?php

namespace App\Robot;

class RobotForUrl extends aRobotForUrl
{
    protected $rules = ['statusCode' => 200, 'length' => 32000, 'directives' => ['Host', 'Sitemap']];
    protected $result = [];
    protected $availableResults = [
        'statusCode' => [
            'Ok' => ['state' => 'Файл robots.txt отдаёт код ответа сервера 200',
                'recommendation' => 'Доработки не требуются'],
            'error' => ['state' => 'При обращении к файлу robots.txt сервер возвращает код ответа: ',
                'recommendation' => 'Программист: Файл robots.txt должны отдавать код ответа 200, иначе файл не будет 
                обрабатываться. Необходимо настроить сайт таким образом, чтобы при обращении к файлу robots.txt сервер 
                возвращает код ответа 200'],
        ],
        'length' => [
            'Ok' => ['state' => 'Размер файла robots.txt составляет __, что находится в пределах допустимой нормы',
                'recommendation' => 'Доработки не требуются'],
            'error' => ['state' => 'Размера файла robots.txt составляет __, что превышает допустимую норму',
                'recommendation' => 'Программист: Максимально допустимый размер файла robots.txt составляем 32 кб. 
                Необходимо отредактировть файл robots.txt таким образом, чтобы его размер не превышал 32 Кб'],
        ],
        'robot' => [
            'Ok' => ['state' => 'Файл robots.txt присутствует', 'recommendation' => 'Доработки не требуются'],
            'error' => ['state' => 'Файл robots.txt отсутствует',
                'recommendation' => 'Программист: Создать файл robots.txt и разместить его на сайте'],
        ],
        'directives' => [
            'Host' => [
                [
                    'Ok' => ['state' => 'Директива Host указана',
                        'recommendation' => 'Доработки не требуются'],
                    'error' => ['state' => 'В файле robots.txt не указана директива Host',
                        'recommendation' => 'Программист: Для того, чтобы поисковые системы знали, какая версия сайта 
                        является основных зеркалом, необходимо прописать адрес основного зеркала в директиве Host. В 
                        данный момент это не прописано. Необходимо добавить в файл robots.txt директиву Host. Директива 
                        Host задётся в файле 1 раз, после всех правил.']
                ],
                [
                    'Ok' => ['state' => 'В файле прописана 1 директива Host',
                        'recommendation' => 'Доработки не требуются'],
                    'error' => ['state' => 'В файле прописано несколько директив Host',
                        'recommendation' => 'Программист: Директива Host должна быть указана в файле толоко 1 раз. 
                        Необходимо удалить все дополнительные директивы Host и оставить только 1, корректную и 
                        соответствующую основному зеркалу сайта']
                ]
            ],
            'Sitemap' => [[
                'Ok' => ['state' => 'Директива Sitemap указана',
                    'recommendation' => 'Доработки не требуются'],
                'error' => ['state' => 'В файле robots.txt не указана директива Sitemap',
                    'recommendation' => 'Программист: Добавить в файл robots.txt директиву Sitemap']]]
        ]];
    protected $headerStatus;
    protected $tests = [
        1 => 'Проверка наличия файла robots.txt',
        6 => 'Проверка указания директивы Host',
        8 => 'Проверка количества директив Host, прописанных в файле',
        10 => 'Проверка размера файла robots.txt',
        11 => 'Проверка указания директивы Sitemap',
        12 => 'Проверка кода ответа сервера для файла robots.txt'
    ];

    public function __construct($url)
    {
        parent::__construct($url);
        $this->run();
    }

    protected function checkStatusCode($header)
    {
        $code = $this->getStatusCode($header);
        if ($code == $this->rules['statusCode']) {
            $this->result['statusCode'] = ['Ok' => $this->availableResults['statusCode']['Ok']];
        } else {
            $this->result['statusCode'] = ['error' => $this->availableResults['statusCode']['error']];
            $this->result['statusCode']['error']['state'] .= $code;
        }
        return $this;
    }

    protected function checkLength($header, $content)
    {
        $length = $this->getSize($header, $content);
        if ($length <= $this->rules['length']) {
            $this->result['length'] = ['Ok' => $this->availableResults['length']['Ok']];
            $this->result['length']['Ok']['state'] =
                str_replace('__', round($length / 1000, 1) . ' кб', $this->result['length']['Ok']['state']);
        } else {
            $this->result['length'] = ['error' => $this->availableResults['length']['error']];
            $this->result['length']['error']['state'] =
                str_replace('__', round($length / 1000, 1) . ' кб', $this->result['length']['error']['state']);
        }
    }

    protected function checkDirectives($content)
    {
        foreach ($this->rules['directives'] as $directive) {
            $number = $this->getCount($directive, $content);
            if ($number > 0) {
                $this->result[$directive] =
                    ['presence' => ['Ok' => $this->availableResults['directives'][$directive][0]['Ok']]];
                if ($directive == 'Host') {
                    if ($number == 1) {
                        $this->result[$directive] =
                            array_merge($this->result[$directive], ['number' => ['Ok' =>
                                $this->availableResults['directives'][$directive][1]['Ok']]]);
                    } else {
                        $this->result[$directive] =
                            array_merge($this->result[$directive], ['number' => ['error' =>
                                $this->availableResults['directives'][$directive][1]['error']]]);
                    }
                }
            } else {
                $this->result[$directive] =
                    ['presence' => ['error' => $this->availableResults['directives'][$directive][0]['error']]];
            }
        }
        return $this;
    }

    protected function run()
    {
        if($this->makeRequest()){
            $this->headerStatus = $this->getHeaderWithStatus();
            $status = $this->getStatusCode($this->headerStatus);
            if ($status < 300 && $this->getRobot()) {
                $this->result['robot'] = ['Ok' => $this->availableResults['robot']['Ok']];
                return $this;
            }
            $this->result['robot'] = ['error' => $this->availableResults['robot']['error']];
        }
        return $this;
    }

    protected function formatResult()
    {
        $result = [];
        if (array_key_exists('statusCode', $this->result)) {
            $result[12] = ['test' => $this->tests[12]];
            $result[12] =
                array_key_exists('Ok', $this->result['statusCode']) ?
                    array_merge($result[12],
                        ['status' => 'Оk', 'state' => $this->result['statusCode']['Ok']['state'],
                            'recommendation' => $this->result['statusCode']['Ok']['recommendation']]
                    )
                    :
                    array_merge($result[12],
                        ['status' => 'Ошибка', 'state' => $this->result['statusCode']['error']['state'],
                            'recommendation' => $this->result['statusCode']['error']['recommendation']]
                    );
        }
        if (array_key_exists('robot', $this->result)) {
            $result[1] = ['test' => $this->tests[1]];
            $result[1] =
                array_key_exists('Ok', $this->result['robot']) ?
                    array_merge($result[1],
                        ['status' => 'Оk', 'state' => $this->result['robot']['Ok']['state'],
                            'recommendation' => $this->result['robot']['Ok']['recommendation']]
                    )
                    :
                    array_merge($result[1],
                        ['status' => 'Ошибка', 'state' => $this->result['robot']['error']['state'],
                            'recommendation' => $this->result['robot']['error']['recommendation']]
                    );
        } else {
            return $result;
        }
        if (array_key_exists('length', $this->result)) {
            $result[10] = ['test' => $this->tests[10]];
            $result[10] =
                array_key_exists('Ok', $this->result['length']) ?
                    array_merge($result[10],
                        ['status' => 'Оk', 'state' => $this->result['length']['Ok']['state'],
                            'recommendation' => $this->result['length']['Ok']['recommendation']]
                    )
                    :
                    array_merge($result[10],
                        ['status' => 'Ошибка', 'state' => $this->result['length']['error']['state'],
                            'recommendation' => $this->result['length']['error']['recommendation']]
                    );
        }
        if (array_key_exists('Host', $this->result)) {
            $result[6] = ['test' => $this->tests[6]];
            $result[6] =
                array_key_exists('Ok', $this->result['Host']['presence']) ?
                    array_merge($result[6],
                        ['status' => 'Оk', 'state' => $this->result['Host']['presence']['Ok']['state'],
                            'recommendation' => $this->result['Host']['presence']['Ok']['recommendation']]
                    )
                    :
                    array_merge($result[6],
                        ['status' => 'Ошибка', 'state' => $this->result['Host']['presence']['error']['state'],
                            'recommendation' => $this->result['Host']['presence']['error']['recommendation']]
                    );
        }
        if (array_key_exists('Sitemap', $this->result)) {
            $result[11] = ['test' => $this->tests[11]];
            $result[11] =
                array_key_exists('Ok', $this->result['Sitemap']['presence']) ?
                    array_merge($result[11],
                        ['status' => 'Оk', 'state' => $this->result['Sitemap']['presence']['Ok']['state'],
                            'recommendation' => $this->result['Sitemap']['presence']['Ok']['recommendation']]
                    )
                    :
                    array_merge($result[11],
                        ['status' => 'Ошибка', 'state' => $this->result['Sitemap']['presence']['error']['state'],
                            'recommendation' => $this->result['Sitemap']['presence']['error']['recommendation']]
                    );
        }
        if (array_key_exists('Host', $this->result) && array_key_exists('number', $this->result['Host'])) {
            $result[8] = ['test' => $this->tests[8]];
            $result[8] =
                array_key_exists('Ok', $this->result['Host']['number']) ?
                    array_merge($result[8],
                        ['status' => 'Оk', 'state' => $this->result['Host']['number']['Ok']['state'],
                            'recommendation' => $this->result['Host']['number']['Ok']['recommendation']]
                    )
                    :
                    array_merge($result[8],
                        ['status' => 'Ошибка', 'state' => $this->result['Host']['number']['error']['state'],
                            'recommendation' => $this->result['Host']['number']['error']['recommendation']]
                    );
        }
        return $result;
    }
}