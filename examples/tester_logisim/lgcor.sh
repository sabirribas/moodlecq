#!/bin/bash
#Exemplo de chamada: ./lgcor.sh -t tester.circ -s solution.circ -q query.circ -r RAM.txt
#Obs: Bash é case sensitive

LOGISIM="/usr/bin/logisim-generic-2.7.1-dac1.1.jar"

while getopts t:s:q:r: opt
do
	case $opt
	in
		t) TEST_CIRCUIT=${OPTARG};;
		s) TEST_SOLUTION=${OPTARG};;
		q) TEST_QUERY=${OPTARG};;
		r) RAM=${OPTARG};;
		?) echo "Usage: ./lgcor.sh -t tester.circ -s solution.circ -q query.circ -r ram.txt"
		   exit;;
	esac
done

#Gera a saida base para comparação e a saida para o circuito sendo testado
SOLUTION="java -jar $LOGISIM $TEST_CIRCUIT -tty table -load $RAM" 
${SOLUTION} > _aux1.txt
sed '2d' _aux1.txt > sol_output.txt

TEST="java -jar $LOGISIM $TEST_CIRCUIT -tty table -load $RAM -sub $TEST_SOLUTION $TEST_QUERY"
${TEST} > _aux2.txt
sed '2d' _aux2.txt > test_output.txt


#Compara os dois arquivos e imprime porcentagem de acertos
if `diff sol_output.txt test_output.txt >/dev/null` && [ -s sol_output.txt ] && [ -s test_output.txt ]; then
	echo "GRADE:1" 		#Circuito functiona corretamente
else
	sed -n '8p' < sol_output.txt
	diff -u sol_output.txt test_output.txt
	diff -u sol_output.txt test_output.txt > result.txt

	count=$(wc -l < sol_output.txt)
	err=$(grep -c '+' result.txt)

	err=$((err -=2))
	count=$((count -= 9))
	res=$(echo "scale=2; 1 - $err/$count" | bc -l)
	echo "GRADE:$res" 	#Circuito não funciona corretamente
	rm -f result.txt
fi

#Remove arquivos temporários gerados durante a execução
rm -f _aux1.txt
rm -f _aux2.txt
rm -f sol_output.txt
rm -f test_output.txt
