<?php
function gerarCpf() {
    $n = [];
    for($i=0;$i<9;$i++) $n[] = rand(0,9);
    $s1 = 0;
    for($i=0;$i<9;$i++) $s1 += $n[$i]*(10-$i);
    $d1 = ($s1*10)%11; if($d1==10||$d1==11) $d1=0;
    $n[] = $d1;
    $s2 = 0;
    for($i=0;$i<10;$i++) $s2 += $n[$i]*(11-$i);
    $d2 = ($s2*10)%11; if($d2==10||$d2==11) $d2=0;
    $n[] = $d2;
    return implode('',$n);
}
echo gerarCpf()."\n".gerarCpf()."\n";
