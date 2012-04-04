<?php
require('lib/fpdf.php');
$val='';
if(isset($_POST['url']))
       {$_POST['url']=str_replace("http://","",$_POST['url']);
       $url=explode('/',$_POST['url']);
       $url[0]=str_replace(".","-",$url[0]);
       }

if($_POST['type']=='pdf')
{

class PDF extends FPDF
{
// Load data
function LoadData()
{
$lines= array();
$total=explode('^',$_POST['total']);
for($i=0;$i<$total[1];$i++)
{$x='post_error'.$i;
$lines[$i]=$_POST[$x];
}
for($j=0;$j<$total[2];$j++)
{$x='post_l_prob'.$j;
$lines[$i]=$_POST[$x];
$i++;
}
for($k=0;$k<$total[3];$k++)
{$x='post_p_prob'.$k;
$lines[$i]=$_POST[$x];
$i++;}
    // Read file lines
    $data = array();
   foreach($lines as $line)
        $data[] = explode('^',trim($line));
    return $data;
}

// Colored table
function FancyTable($header, $data)
{
    // Colors, line width and bold font
    $this->SetFillColor(255,0,0);
    $this->SetTextColor(255);
    $this->SetDrawColor(128,0,0);
    $this->SetLineWidth(.3);
    $this->SetFont('','B');
    // Header
    $w = array(60, 60, 60);
    for($i=0;$i<count($header);$i++)
        $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
    $this->Ln();
    // Color and font restoration
    $this->SetFillColor(224,235,255);
    $this->SetTextColor(0);
    $this->SetFont('');
    // Data
    $fill = false;
    foreach($data as $row)
    {   $html=str_replace("\\",'',$row[3]);
    $html=str_replace("  ","",$html);
        $html=trim($html,"\t");
         $this->SetX(10); 
        $this->SetFillColor(0,0,0);
        $this->write(6,$row[0]);
        $this->SetX(80); 
        $this->write(6,$row[1]);
        $this->SetX(150); 
        $this->write(6,$row[2]);
        $this->Ln();
        $this->SetFillColor(224,235,255);
        $this->WriteHTML($html);
        $this->Ln();$this->Ln();
        $fill = !$fill;
    }
    // Closing line
    $this->Cell(array_sum($w),0,'','T');
	}

var $B;
var $I;
var $U;
var $HREF;

function PDF($orientation='P', $unit='mm', $size='A4')
{
    // Call parent constructor
    $this->FPDF($orientation,$unit,$size);
    // Initialization
    $this->B = 0;
    $this->I = 0;
    $this->U = 0;
    $this->HREF = '';
}

function WriteHTML($html)
{
    // HTML parser
    $html = str_replace("\n",' ',$html);
    $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
    foreach($a as $i=>$e)
    {
        if($i%2==0)
        {
            // Text
            if($this->HREF)
                $this->PutLink($this->HREF,$e);
            else
                $this->Write(5,$e);
        }
        else
        {
            // Tag
            if($e[0]=='/')
                $this->CloseTag(strtoupper(substr($e,1)));
            else
            {
                // Extract attributes
                $a2 = explode(' ',$e);
                $tag = strtoupper(array_shift($a2));
                $attr = array();
                foreach($a2 as $v)
                {
                    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                        $attr[strtoupper($a3[1])] = $a3[2];
                }
                $this->OpenTag($tag,$attr);
            }
        }
    }
}

function OpenTag($tag, $attr)
{
    // Opening tag
    if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,true);
    if($tag=='A')
        $this->HREF = $attr['HREF'];
    if($tag=='BR')
        $this->Ln(5);
}

function CloseTag($tag)
{
    // Closing tag
    if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,false);
    if($tag=='A')
        $this->HREF = '';
}

function SetStyle($tag, $enable)
{
    // Modify style and select corresponding font
    $this->$tag += ($enable ? 1 : -1);
    $style = '';
    foreach(array('B', 'I', 'U') as $s)
    {
        if($this->$s>0)
            $style .= $s;
    }
    $this->SetFont('',$style);
}

function PutLink($URL, $txt)
{
    // Put a hyperlink
    $this->SetTextColor(0,0,255);
    $this->SetStyle('U',true);
    $this->Write(5,$txt,$URL);
    $this->SetStyle('U',false);
    $this->SetTextColor(0);
}
}

$pdf = new PDF();

$pdf->SetAuthor('Selvam Palanimalai');
$pdf->SetTitle('Achecker Report');
$pdf->SetFont('Helvetica','B',20);
$pdf->SetTextColor(60,60,200);
$pdf->AddPage('P');
$pdf->SetDisplayMode('real','default');

$pdf->Image('images/logo.jpg',50,100,100);

$pdf->SetXY(50,50);
//$pdf->SetDrawColor(50,60,100);
$pdf->Cell(100,10,'ACHECKER REPORT',1,0,'C',0);
$pdf->SetXY(80,150);
$pdf->SetFontSize(10);
$pdf->Write(10,"URL : ".$_POST['url']);
$pdf->SetXY(80,170);
$pdf->SetFontSize(10);
$pdf->Write(10,"GUIDELINES : ".$_POST['guide']);
$pdf->SetXY(20,200);
$pdf->SetFontSize(20);
$pdf->Write(10,'Created Using Achecker Opensocial Gadget.');

// Column headings
$header = array('Error Type', 'Column No', 'Line No');
// Data loading

$data = $pdf->LoadData();
//print_r($data);
$pdf->SetFont('Arial','',14);
$pdf->AddPage();
$pdf->FancyTable($header,$data);
$name='pdf_report('.$url[0].'_'.date("F-j-Y").').pdf';

$pdf->Output($name,'D');
}

if($_POST['type']=='csv')
{

$fname = 'temp/csv_report('.$url[0].'_'.date("F-j-Y").').csv';
$fp = fopen($fname,'w');
fwrite($fp," ");
$total=explode('^',$_POST['total']);
$data1=array('TOTAL :',$total[0]);
fputcsv($fp, $data1);
$data2=array('URL :',$_POST['url']);
fputcsv($fp, $data2);
$empty=array(' ',' ',' ',' ');
fputcsv($fp, $empty);
$data3=array('TYPE','COLUMN','LINE','MESSAGE');
fputcsv($fp, $data3);

for($i=0;$i<$total[1];$i++)
      {$x='post_error'.$i;
       $data=explode("^",$_POST[$x]);
       $html=str_replace("\\",'',$data[3]);
       $html=str_replace("\n","",$html);
       $html=str_replace("  "," ",$html);      
       $data[3]=$html;
       fputcsv($fp, $data);
       //echo $data[2]."<br/>";
       }
for($i=0;$i<$total[2];$i++)
      {$x='post_l_prob'.$i;
       $data=explode("^",$_POST[$x]);
       $html=str_replace("\\",'',$data[3]);
       $html=str_replace("\n","",$html);
       $html=str_replace("  "," ",$html);
       $data[3]=$html;
       //echo $data[2]."<br/>";
       fputcsv($fp, $data);
      }
for($i=0;$i<$total[3];$i++)
  {$x='post_p_prob'.$i;
       $data=explode("^",$_POST[$x]);
       $html=str_replace("\\",'',$data[3]);
       $html=str_replace("\n","",$html);
       $html=str_replace("  "," ",$html);
       $data[3]=$html;
      // echo $data[2]."<br/>";
       fputcsv($fp, $data);
      }

fclose($fp);
$fname=str_replace("temp/","",$fname);
header('Content-type: application/csv');
header("Content-Disposition: inline; filename=".$fname);
readfile("temp/".$fname);
}


if($_POST['type']=='html')
{

$_POST['uri_form']=str_replace('output=rest','output=html',$_POST['uri_form']);
$val= file_get_contents($_POST['uri_form']);
$_POST['uri_form']=str_replace("http://"," ",$_POST['uri_form']);
$url=explode('/',$_POST['uri_form']);
$url[0]=str_replace(".","-",$url[0]);
$name="temp/html_report(".$url[0].'_'.date("F-j-Y").").html";
$name=str_replace(" ","",$name);
file_put_contents($name,$val);
$name=str_replace("temp/","",$name);
header('Content-disposition: attachment; filename='.$name.'');
header('Content-type: application/html');
readfile("temp/".$name);
}

if($_POST['type']=='xml')
{
$val= file_get_contents($_POST['uri_form']);
$_POST['uri_form']=str_replace("http://"," ",$_POST['uri_form']);
$url=explode('/',$_POST['uri_form']);
$url[0]=str_replace(".","-",$url[0]);
$name="temp/xml_report(".$url[0].'_'.date("F-j-Y").").xml";
$name=str_replace(" ","",$name);
file_put_contents($name,$val);

$name=str_replace("temp/","",$name);
$name=str_replace(" ","",$name);
header('Content-disposition: attachment; filename='.$name.'');
header('Content-type: application/xml');
readfile("temp/".$name);
}

if($_POST['type']=='earl')
{
$head='<?xml version="1.0" encoding="UTF-8" ?>

<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns:earl="http://www.w3.org/ns/earl#"
    xmlns:dct="http://purl.org/dc/terms/"
	xml:base="http://www.example.org/earl/report#">';


$software='<earl:Software rdf:about="http://achecker.ca/#">
    <dct:title xml:lang="en">Achecker gadget HTML Validator</dct:title>
    <dct:hasVersion>1.0.0</dct:hasVersion>
    <dct:description xml:lang="en">
    This tool checks single HTML pages for conformance with accessibility standards to ensure the content can be accessed by everyone.
    </dct:description>
  </earl:Software>';

$testSubject= '<rdf:Description rdf:about="'.$_POST['url'].'">
    <dct:title xml:lang="en">Achecker Report</dct:title>
    <dct:date rdf:datatype="http://www.w3.org/2001/XMLSchema#date">'.date('l jS \of F Y h:i:s A').'</dct:date>
    <rdf:type rdf:resource="http://www.w3.org/ns/earl#TestSubject"/>
  </rdf:Description>';
    
    
$testRequirements='<earl:TestRequirement rdf:about="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <dct:title xml:lang="en">'.$_POST['guide'].'</dct:title>
    <dct:description xml:lang="en">'.$_POST['guide'].'</dct:description>
  </earl:TestRequirement>';
//:::::::::::: parser to get the errors in order ::::::::::::::
$errors='';
	$total=explode('^',$_POST['total']);
	for($i=0;$i<$total[1];$i++)
		{$x='post_error'.$i;
		$array=explode("^",$_POST[$x]);
		
 $temp='
<earl:TestResult rdf:ID="error1">
    <dct:description rdf:parseType="Literal">
      <div xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" kj>
        <p>'.$array[0].'- Line '.$array[2].' column '.$array[1].' Error message:'.$array[3].' Error Source Code : '.$array[4].' </p>
      </div>
    </dct:description>
    <earl:outcome rdf:resource="http://www.w3.org/ns/earl#failed" />
  </earl:TestResult>';

$errors=$errors.$temp;
		}
for($i=0;$i<$total[2];$i++)
{$x='post_l_prob'.$i;$array=explode("^",$_POST[$x]);
 $temp='
<earl:TestResult rdf:ID="error1">
    <dct:description rdf:parseType="Literal">
      <div xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
        <p>'.$array[0].'- Line '.$array[2].' column '.$array[1].' Error message:'.$array[3].' Error Source Code : '.$array[4].' </p>
      </div>
    </dct:description>
    <earl:outcome rdf:resource="http://www.w3.org/ns/earl#failed" />
  </earl:TestResult>';

$errors=$errors.$temp;
}
for($i=0;$i<$total[3];$i++)
{$x='post_p_prob'.$i;
$array=explode("^",$_POST[$x]);
 $temp='
<earl:TestResult rdf:ID="error1">
    <dct:description rdf:parseType="Literal">
      <div xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
        <p>'.$array[0].'- Line '.$array[2].' column '.$array[1].' Error message:'.$array[3].' Error Source Code : '.$array[4].' </p>
      </div>
    </dct:description>
    <earl:outcome rdf:resource="http://www.w3.org/ns/earl#failed" />
  </earl:TestResult>';

$errors=$errors.$temp;
}
$val=$head.$software.$testSubject.$testRequirements.$errors.'  </rdf:RDF>';
$name='temp/earl_report('.$url[0].'_'.date("F-j-Y").').xml';
file_put_contents($name,$val);
$name=str_replace("temp/","",$name);
header('Content-disposition: attachment; filename='.$name.'');
header('Content-type: application/xml');
readfile("temp/".$name);
}

?>