// Incluíndo código do estudante
exec('soma.sci');

// Iniciando número de testes e sucessos
tests=0;success=0;

// Testes
if ( soma(1,2) == 3 ) success = success + 1; end; tests = tests + 1;
if ( soma(3,2) == 5 ) success = success + 1; end; tests = tests + 1;
if ( soma(-10,9) == -1 ) success = success + 1; end; tests = tests + 1;

// A linha abaixo precisa ser a última impressa pelo 'make run'
printf("GRADE:%f\n",success/tests);

exit;

