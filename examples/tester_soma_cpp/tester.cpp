#include <iostream>
using namespace std;

/* Incluíndo código do estudante */
#include "soma.h"

int main()
{
	/* Iniciando número de testes e sucessos */
	int tests(0) , success(0) ;
	
	/* Testes */
	if ( soma(1,2) == 3 ) success++; tests++;
	if ( soma(3,2) == 5 ) success++; tests++;
	if ( soma(-10,9) == -1 ) success++; tests++;

	// A linha abaixo precisa ser a última impressa pelo 'make run'
	cout << "GRADE:" << float(success) / float(tests) << endl;
	return 0;
}
