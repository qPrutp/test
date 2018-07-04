<?php

class Save
{
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

    function __construct($result2, $phpexcel)
    {
        $this->run($result2, $phpexcel);
    }

    protected function run($result2, $phpexcel) {

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

        foreach ($result2 as $k => $row) {
        	$phpexcel->getActiveSheet()->mergeCells("A$i:E$i");
        	$phpexcel->getActiveSheet()->getStyle("A$i:E$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        	$phpexcel->getActiveSheet()->getStyle("A$i:E$i")->getFill()->getStartColor()->setRGB('e6e6e6');
        	$phpexcel->getActiveSheet()->getStyle("A$i:E$i")->applyFromArray($styleBorderArray);
        	$i++;
            $phpexcel->getActiveSheet()->setCellValue("A$i", $k);
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
            if ($string == "Оk") {
            	$phpexcel->getActiveSheet()->getStyle("C$i")->getFill()->getStartColor()->setRGB('99e600');
        	} else {
        		$phpexcel->getActiveSheet()->getStyle("C$i")->getFill()->getStartColor()->setRGB('ff704d');
        	}
            $string = STATE_TITLE;
            $phpexcel->getActiveSheet()->setCellValueExplicit("D$i", $string, PHPExcel_Cell_DataType::TYPE_STRING);
            $phpexcel->getActiveSheet()->getStyle("D$i")->getAlignment()->setVertical('center');
            $phpexcel->getActiveSheet()->getStyle("D$i")->applyFromArray($styleBorderArray);
            $string = RCOMENDATION_TITLE;
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
        $filename = "test-sipius-new.xlsx";
        if (file_exists($filename)) {
            unlink($filename);
        }
        $objWriter->save($filename);
    }
}

?>