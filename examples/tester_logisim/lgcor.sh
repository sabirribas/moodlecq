#!/bin/bash
#Exemplo de chamada: ./lgcor.sh -t tester.circ -s solution.circ -q query.txt

LOGISIM="/usr/bin/logisim-generic-2.7.1-dac1.1.jar"
RAM="RAM.txt"
TEST_QUERY="QUERY.circ"

while getopts t:s:q:l: opt
do
	case $opt
	in
		t) TEST_CIRCUIT=${OPTARG};;
		s) TEST_SOLUTION=${OPTARG};;
		q) TEST_QUERY_TEXT=${OPTARG};;
	esac
done

if [ -e $TEST_QUERY ]; then
	rm $TEST_QUERY
fi

while read line
do
	echo "$line" >> "$TEST_QUERY"
done < "$TEST_QUERY_TEXT"


SOLUTION="java -jar $LOGISIM $TEST_CIRCUIT -tty table -load $RAM" 
${SOLUTION} > _aux1.txt
sed '2d' _aux1.txt > sol_output.txt

TEST="java -jar $LOGISIM $TEST_CIRCUIT -tty table -load $RAM -sub $TEST_SOLUTION $TEST_QUERY"
${TEST} > _aux2.txt
sed '2d' _aux2.txt > test_output.txt


if `diff sol_output.txt test_output.txt >/dev/null` && [ -s sol_output.txt ] && [ -s test_output.txt ]; then
	echo "TRUE"
	echo "GRADE:1"
else
	echo "FALSE"
	sed -n '8p' < sol_output.txt
	diff -u sol_output.txt test_output.txt
	diff -u sol_output.txt test_output.txt > result.txt

	count=$(wc -l < sol_output.txt) #Number of inputs
	err=$(grep -c '+' result.txt) #Number of errors

	echo "C: $count E: $err"
	
	let "err -= 2"
	let "count -= 9"
	echo "C: $count E: $err"
	res=$(echo "scale=2; 1 - $err/$count" | bc -l)
	echo "GRADE:$res"
	rm result.txt
fi


rm _aux1.txt
rm _aux2.txt
rm sol_output.txt
rm test_output.txt
rm $TEST_QUERY
