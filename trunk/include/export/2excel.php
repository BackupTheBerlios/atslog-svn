<?

CLASS  MID_SQLPARAExel extends GeraExcel
    {
    #Variaveis da classe GeraEcel
    var $armazena_dados;    // Armazena dados
    var $nomeDoArquivoXls;  // Nome para o arquivo excel

// define parametros(init)
function mid_sqlparaexcel(){

// define nome do arquivo
$this->nomeDoArquivoXls = "atslog_".date("H-i-s");

 }// fecha classe
}

// Gera EXCEL
class  GeraExcel{

// define parametros(init)
function  GeraExcel(){

$this->armazena_dados   = ""; // Armazena dados para imprimir(temporario)
$this->nomeDoArquivoXls = $nomeDoArquivoXls; // Nome do arquivo excel
$this->ExcelStart();
}// fim constructor

     
// Monta cabecario do arquivo(tipo xls)
function ExcelStart(){

//inicio do cabecario do arquivo
$this->armazena_dados = pack( "vvvvvv", 0x809, 0x08, 0x00,0x10, 0x0, 0x0 );
}

// Fim do arquivo excel
function FechaArquivo(){
$this->armazena_dados .= pack( "vv", 0x0A, 0x00);
}


// monta conteudo
function MontaConteudo( $excel_linha, $excel_coluna, $value){

$tamanho = strlen( $value );
$this->armazena_dados .= pack( "v*", 0x0204, 8 + $tamanho, $excel_linha, $excel_coluna, 0x00, $tamanho );
$this->armazena_dados .= $value;
}//Fim, monta Col/Lin

// Gera arquivo(xls)
function GeraArquivo(){

//Fecha arquivo(xls)
$this->FechaArquivo();


header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT");
header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
header ( "Pragma: no-cache" );
header ( "Content-type: application/octet-stream; name=".$this->nomeDoArquivoXls.".xls");
header ( "Content-Disposition: attachment; filename=".$this->nomeDoArquivoXls.".xls"); 
header ( "Content-Description: atslog to excel" );
print  ( $this->armazena_dados);


}// fecha funcao
# Fim da classe que gera excel
}
?>