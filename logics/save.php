<?php

class Save
{
    
    private $result2 = [
        [
            'num' => 1,
            'test' => 'Проверка наличия файла robots.txt',
            'status' => 'Ok',
            'state' => 'Файл robots.txt присутствует',
            'recommendation' => 'Доработки не требуются'],
        [
            'num' => 2,
            'test' => 'Проверка указания директивы Host',
            'status' => 'Ошибка',
            'state' => 'В файле robots.txt не указана директива Host',
            'recommendation' => 'Программист: Для того, чтобы поисковые системы знали, какая версия сайта является основных зеркалом, необходимо прописать адрес основного зеркала в директиве Host. В данный момент это не прописано. Необходимо добавить в файл robots.txt директиву Host. Директива Host задётся в файле 1 раз, после всех правил.'
        ]
    ];
    private $state = 'Cостояние';
    private $recommendations = 'Рекомендации';
    private $title = [
        [
            'name'   => '№',
            'cell'  => 'A'
        ],
        [
            'name'   => 'Название проверки',
            'cell'  => 'B'
        ],
        [
            'name'   => 'Статус',
            'cell'  => 'C'
        ],
        [
            'name'   => '',
            'cell'  => 'D'
        ],
        [
            'name'   => 'Текущее состояние',
            'cell'  => 'E'
        ],
    ];

    function __construct()
    {
        $this->run();
    }

    protected function run() {
        require_once('../Classes/PHPExcel.php');
        $phpexcel = new PHPExcel();

        $phpexcel->getActiveSheet()->getStyle("A2:E2")->applyFromArray(array("font" => array("size" => 15,)));
        $phpexcel->getActiveSheet()->getStyle("A2:E2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $phpexcel->getActiveSheet()->getStyle("A2:E2")->getFill()->getStartColor()->setRGB("a3c2c2");
        $phpexcel->getActiveSheet()->getStyle("A2")->getAlignment()->setHorizontal("center");
        $phpexcel->getActiveSheet()->getStyle("C2")->getAlignment()->setHorizontal("center");

        $styleBorderArray = array(
          'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN,
              'color' => array('rgb' => '737373')
            )
          )
        );
        $phpexcel->getActiveSheet()->getStyle("A2:E2")->applyFromArray($styleBorderArray);

        $phpexcel->getActiveSheet()->getRowDimension(12)->setRowHeight(-1);

        for ($i = 0; $i < count($this->title); $i++) {
            $string = $this->title[$i]['name'];
            $cellLetter = $this->title[$i]['cell'] . 2;
            $phpexcel->getActiveSheet()->setCellValueExplicit($cellLetter, $string, PHPExcel_Cell_DataType::TYPE_STRING);
        }
        $i = 3;

        foreach ($this->result2 as $row) {
        	$phpexcel->getActiveSheet()->mergeCells("A$i:E$i");
        	$phpexcel->getActiveSheet()->getStyle("A$i:E$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        	$phpexcel->getActiveSheet()->getStyle("A$i:E$i")->getFill()->getStartColor()->setRGB('e6e6e6');
        	$phpexcel->getActiveSheet()->getStyle("A$i:E$i")->applyFromArray($styleBorderArray);
        	$i++;
            $phpexcel->getActiveSheet()->setCellValue("A$i", $row['num']);
            $phpexcel->getActiveSheet()->mergeCells("A$i:A".($i+1));
            $phpexcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setHorizontal('center');
            $phpexcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setVertical('center');
            $phpexcel->getActiveSheet()->getStyle("A$i:A".($i+1))->applyFromArray($styleBorderArray);
            $string = $row['test'];
            $phpexcel->getActiveSheet()->mergeCells("B$i:B".($i+1));
            $phpexcel->getActiveSheet()->setCellValueExplicit("B$i", $string, PHPExcel_Cell_DataType::TYPE_STRING);
            $phpexcel->getActiveSheet()->getStyle("B$i")->getAlignment()->setVertical('center');
            $phpexcel->getActiveSheet()->getStyle("B$i:B".($i+1))->applyFromArray($styleBorderArray);
            $string = $row['status'];
            $phpexcel->getActiveSheet()->mergeCells("C$i:C".($i+1));
            $phpexcel->getActiveSheet()->setCellValueExplicit("C$i", $string, PHPExcel_Cell_DataType::TYPE_STRING);
            $phpexcel->getActiveSheet()->getStyle("C$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $phpexcel->getActiveSheet()->getStyle("C$i")->getAlignment()->setHorizontal('center');
            $phpexcel->getActiveSheet()->getStyle("C$i")->getAlignment()->setVertical('center');
            $phpexcel->getActiveSheet()->getStyle("C$i:C".($i+1))->applyFromArray($styleBorderArray);
            if ($string == "Ok") {
            	$phpexcel->getActiveSheet()->getStyle("C$i")->getFill()->getStartColor()->setRGB('99e600');
        	} else {
        		$phpexcel->getActiveSheet()->getStyle("C$i")->getFill()->getStartColor()->setRGB('ff704d');
        	}
            $string = $this->state;
            $phpexcel->getActiveSheet()->setCellValueExplicit("D$i", $string, PHPExcel_Cell_DataType::TYPE_STRING);
            $phpexcel->getActiveSheet()->getStyle("D$i")->getAlignment()->setVertical('center');
            $phpexcel->getActiveSheet()->getStyle("D$i")->applyFromArray($styleBorderArray);
            $string = $this->recommendations;
            $phpexcel->getActiveSheet()->setCellValueExplicit("D".($i+1), $string, PHPExcel_Cell_DataType::TYPE_STRING);
            $phpexcel->getActiveSheet()->getStyle("D".($i+1))->getAlignment()->setVertical('center');
            $phpexcel->getActiveSheet()->getStyle("D".($i+1))->applyFromArray($styleBorderArray);
            $string = $row['state'];
            $phpexcel->getActiveSheet()->setCellValueExplicit("E$i", $string, PHPExcel_Cell_DataType::TYPE_STRING);
            $phpexcel->getActiveSheet()->getStyle("E$i")->applyFromArray($styleBorderArray);
            $string = $row['recommendation'];
            $phpexcel->getActiveSheet()->setCellValueExplicit("E".($i+1), $string, PHPExcel_Cell_DataType::TYPE_STRING);
            $phpexcel->getActiveSheet()->getStyle("E".($i+1))->getAlignment()->setWrapText(true);
            $phpexcel->getActiveSheet()->getStyle("E".($i+1))->applyFromArray($styleBorderArray);
            $i += 2;
        }

        $phpexcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $phpexcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
        $phpexcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $phpexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $phpexcel->getActiveSheet()->getColumnDimension('E')->setWidth(100);
        $page = $phpexcel->setActiveSheetIndex();
        $page->setTitle("test-sipius");
        $objWriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007');
        $filename = "test-sipius.xlsx";
        if (file_exists($filename)) {
            unlink($filename);
        }
        $objWriter->save($filename);
    }
}

new Save();

?>