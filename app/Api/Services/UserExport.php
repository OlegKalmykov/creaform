<?php

namespace App\Api\Services;

use Barryvdh\Snappy\PdfWrapper;

class UserExport
{
    private $pdf;

    public function __construct(PdfWrapper $pdfWrapper)
    {
        $this->pdf = $pdfWrapper;
    }

    public function run($users)
    {
//        $this->pdf->
        $this->pdf->setOption('encoding', 'UTF-8');
        $this->pdf->loadView('pdf.employees', ['users' => $users]);
//        $this->pdf->loadFile('pdf.employees');
//        return $this->pdf->output(/*'employees.pdf'*/);
        return $this->pdf->download('employees.pdf');
    }
}