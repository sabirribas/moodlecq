# Create your views here.

from django.http import HttpResponseRedirect, HttpResponse

import sys,os
path_graderd = os.path.dirname(os.path.abspath(__file__))+'/../../graderd'
sys.path.append(path_graderd)
import graderd
import logging
logger = logging.getLogger(__name__)

def test(request):
	content = "This is only a test!"
	return HttpResponse( content )

''' rpctcpclient.py '''

import socket
import json

class RPCTCPClient:
	def call(self,method,params):

		content = {"method":method,"params":params}

		# setup
		HOST = '127.0.0.1'     # Endereco IP do Servidor
		PORT = 5000            # Porta que o Servidor esta
		tcp = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
		dest = (HOST, PORT)
		tcp.connect(dest)

		# send
		msg = json.dumps(content)
		tcp.send (msg)

		# recv
		msg = tcp.recv(1048576)

		# close
		tcp.close()

		return msg

def test_socket(request):

	rpcclient = RPCTCPClient()

	method,params = 'testmethod',{}
	method,params = 'testcode',{'lang':'py','code':'a=int(raw_input(""))\nb=int(raw_input(""))\nprint a+b','tests':[('1\n2\n','3\n'),('2\n3\n','4\n')]}
	method,params = 'testcode',{'lang':'cpp','code':"#include <iostream>\nusing namespace std;\nint main()\n{\n\tint a,b;\n\tcin >> a >> b;\n\tcout << a+b << endl;\n\treturn 0;\n}",'tests':[('1\n2\n','3\n'),('2\n3\n','4\n')]}

	#result = rpcclient.call(method,params)
	result = graderd.testcode(params)

	#return HttpResponse( method + ' ' + str(params) + '<br/>' + str(result) )

	#resultvalue = json.loads(result)
	resultvalue = result

	resultjson = {
		'method':method,
		'params':params,
		'result':resultvalue,
		'score' : sum( resultvalue['success'] ) / float( len( resultvalue['success']) ) ,
	}
	#'score' : float(resultvalue['success'].count(True)) / float(len(resultvalue['success'])) ,

	return HttpResponse( json.dumps(resultjson) , mimetype="application/json" )


from django.views.decorators.csrf import csrf_exempt
@csrf_exempt
def testcode(request):

	if not request.POST.has_key('lang') or not request.POST.has_key('code') or not request.POST.has_key('tests'):
		return HttpResponse( json.dumps( {'error':'ERROR IN POST'} ) , mimetype="application/json" )

	lang = request.POST['lang']
	code = request.POST['code']
	tests = request.POST['tests']

	print '= TESTCODE ='

	print 'lang:\n%s\n\ncode:\n%s\n\ntests:\n%s'%(lang,code,str(tests))
	#return HttpResponse('Hello world')

	rpcclient = RPCTCPClient()

	#tests = [('1\n2\n','3\n'),('2\n3\n','4\n')]

	method,params = 'testcode',{'lang':lang,'code':code,'tests':json.loads(tests)}

	#result = rpcclient.call(method,params)
	result = graderd.testcode(params)

	#resultvalue = json.loads(result)
	resultvalue = result

	resultjson = {
		'method':method,
		'params':params,
		'result':resultvalue,
		'score' : sum( resultvalue['success'] ) / float( len( resultvalue['success']) ) ,
	}
	#'score' : float(resultvalue['success'].count(True)) / float(len(resultvalue['success'])) ,

	#logger.info('\n\n===============\n\n')
	#logger.info(json.dumps(resultjson)+'\n\n')
	print json.dumps(resultjson)

	return HttpResponse( json.dumps(resultjson) , mimetype="application/json" )










'''
test

cd moodlecq/grader
ipython manage.py shell
path_graderd = '/home/sabir/moodlecq/graderd' ; import sys,os ; sys.path.append(path_graderd) ; import graderd
code = "function s = soma(a,b)\ns=a+b;\nendfunction\n\n//sce"; params = {'lang':'sce','code':code,'tests':[['__TESTER__','http://homepages.dcc.ufmg.br/~sabir/grader/tester_soma_sce.zip']]}; result = graderd.testcode(params); result
code = "int soma(int a,int b){return a+b;}"; params = {'lang':'cpp','code':code,'tests':[['__TESTER__','http://homepages.dcc.ufmg.br/~sabir/grader/tester_soma_cpp.zip']]}; result = graderd.testcode(params); result
'''



