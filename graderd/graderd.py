import socket
import sys
import re

''' rpc.py '''

class RPC:
	methods = {}

	def init(self,params):
		return
	def serve(self):
		return
	def finish(self):
		return
	def run(self,params):
		return
	def addmethod(self,methodname,method):
		self.methods[methodname] = method
		return

''' rpctcp.py '''

import json

class RPCTCP(RPC):

	def init(self,init_params):

		self.init_params = init_params

		HOST = ''
		PORT = self.init_params['port']
		self.orig = (HOST, PORT)
		self.tcp = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
		self.tcp.bind(self.orig)
		self.tcp.listen(1)

		while True: self.serve()

		return

	def serve(self):

		try:
			self.con, self.cliente = self.tcp.accept()
			print 'Concetado por', self.cliente
		        msg = self.con.recv(1024)
			msgjson = json.loads(msg)
			method  = msgjson['method']
			params  = msgjson['params']
			self.run(method,params,self.cliente,self.con)
			self.con.close()
		except:
			self.con.close()

	def finish(self):
		return

	def run(self,method,params,cliente,con):

	        print cliente, method, params

		result = self.methods[method](params)
		resultjson = json.dumps(result)

		con.send(resultjson)

		f = open('out','a')
		f.write(cliente[0]+' '+method+'\n')
		f.close()

		return

''' codetester.py '''

class CodeTester:
	def __init__(self,code):
		return
	def comp(self):
		return
	def test(self,test_in,test_out):
		return

''' codetesters.py '''

import os

def call_system(cmd,tmpfile='/tmp/call_system'):
    os.system(cmd+' > '+tmpfile)
    f = open(tmpfile,'r')
    tmp = f.read()
    f.close()
    os.system('rm '+tmpfile)
    return tmp

import random

class CodeTesterDiffPy(CodeTester):

	def __init__(self,code):
		print "write code in disk"
		self.codefilename = '/tmp/main_%i.py' % random.randint(0,1000000) # TODO lang specific
		f = open(self.codefilename,'w')
		f.write(code)
		f.close()
		return

	def clear(self):
		os.system("rm '%s'"%self.codefilename)
		os.system("rm '%s.in'"%self.codefilename)
		os.system("rm '%s.call_system'"%self.codefilename)

	def comp(self):
		print "Compile"
		return

	def test(self,test_in,test_out):

		# write input in disk
		print "write input in disk"
		f = open(self.codefilename+'.in','w')
		f.write(test_in)
		f.close()

		# run
		print "run"
		cmd = 'python %s < %s' % (self.codefilename , self.codefilename+'.in') # TODO lang specific
		print cmd
		student_out = call_system( cmd , '%s.call_system'%self.codefilename )
		print student_out

		return student_out.split() == test_out.split()

class CodeTesterDiffCpp(CodeTester):

	def __init__(self,code):
		print "write code in disk"
		self.codefilename = '/tmp/main_%i.cpp' % random.randint(0,1000000) # TODO lang specific
		f = open(self.codefilename,'w')
		f.write(code)
		f.close()
		return

        def clear(self):
                os.system("rm '%s'"%self.codefilename)
                os.system("rm '%s.in'"%self.codefilename)
                os.system("rm '%s.exe'"%self.codefilename)
		os.system("rm '%s.call_system'"%self.codefilename)

	def comp(self):
		print "Compile"
		cmd = 'g++ %s -o %s.exe' % (self.codefilename , self.codefilename)
		call_system( cmd , '%s.call_system'%self.codefilename )
		return

	def test(self,test_in,test_out):

		# write input in disk
		print "write input in disk"
		f = open(self.codefilename+'.in','w')
		f.write(test_in)
		f.close()

		# run
		print "run"
		cmd = '%s.exe < %s' % (self.codefilename , self.codefilename+'.in') # TODO lang specific
		print cmd
		student_out = call_system( cmd , '%s.call_system'%self.codefilename )
		print student_out

		return student_out.split() == test_out.split()

class CodeTesterDiffSce(CodeTester):

	def __init__(self,code):
		print "write code in disk"
		self.codefilename = '/tmp/main_%i.sce' % random.randint(0,1000000) # TODO lang specific
		f = open(self.codefilename,'w')
		f.write(code+'\n\nexit();')
		f.close()
		return

        def clear(self):
                os.system("rm '%s'"%self.codefilename)
                os.system("rm '%s.in'"%self.codefilename)
		os.system("rm '%s.call_system'"%self.codefilename)

	def comp(self):
		print "Compile"
		return

	def test(self,test_in,test_out):

		# write input in disk
		print "write input in disk"
		f = open(self.codefilename+'.in','w')
		f.write(test_in)
		f.close()

		# run
		print "run"
		cmd = 'scilab -nogui -nwni -nb -f %s < %s' % (self.codefilename , self.codefilename+'.in') # TODO lang specific
		print cmd
		student_out = call_system( cmd , '%s.call_system'%self.codefilename )
		print student_out

		#print student_out.split(),' == ',test_in.split() + test_out.split()

		# ['\x1b[?1h\x1b=2', '\x1b[?1l\x1b>\x1b[?1h\x1b=3', '\x1b[?1l\x1b>5']

		student_out_split = filter(lambda s: not re.match('\x1b[^\=]*\=', s), student_out.split()) # remove input
		student_out_split = map(lambda s: re.sub('\x1b[^\>]*\>', '', s), student_out_split) # fix output

		print student_out_split,' == ',test_out.split()

		result = student_out_split == test_out.split()
		print result

		return result

class CodeTesterExternal(CodeTester):

	def __init__(self,tester_url,lang,code):

		print "downloading tester"
		self.codefilename = '/tmp/main_%i_%s' % (random.randint(0,1000000),lang) # TODO lang specific
		self.testerdir = '%s.tester/' % self.codefilename
		call_system('mkdir %s' % self.testerdir)
		call_system('wget -P "%s" "%s"' % (self.testerdir,tester_url) )
		call_system('unzip "%s*.zip" -d "%s"' % (self.testerdir,self.testerdir) )
		
		print "replacing __USERCODE__"

		with open('%s/__USERCODE__'%self.testerdir,'w') as f: f.write( code )

		return

	def clear(self):

		os.system("rm -r '%s'" % self.testerdir)

	def comp(self):
		print "Compile"
		os.system('make tester -C "%s"' % self.testerdir)
		return

	def test(self,test_in,test_out):

		# run
		print "run"
	
		result = call_system('make run -C "%s"' % self.testerdir)

		result = filter( lambda x: not ( len(x.strip()) == 0 or 
			len(x)>=5 and x[:5]=='make:') , result.split('\n'))

		return result[-1].split()[0] == '1'

		#return student_out.split() == test_out.split()


''' methods.py '''

def testmethod(params):
	return {'return':1}

def testcode(params):

	#testpath,solpath,testfile,testfileout

	lang  = params['lang']
	code  = params['code']
	tests = params['tests']

	print lang,code,tests

	# external tester: [["__TESTER__","http://...zip"]]
	if tests[0][0] == '__TESTER__':
		codetester = CodeTesterExternal(tests[0][1],lang,code)

	# standard io
	elif lang == 'py': codetester = CodeTesterDiffPy(code)
	elif lang == 'cpp': codetester = CodeTesterDiffCpp(code)
	elif lang == 'sce': codetester = CodeTesterDiffSce(code)
	else: 
		print "ERROR"
		return

	# compile code
	codetester.comp()

	# for each test run code
	success = []
	for test in tests:

		print '#test',test
		success.append( codetester.test(test[0],test[1]) )

	codetester.clear()

	return {'success':success}

def wp_consult(params):
	return {'consult':[('Word','ord',10)]}

''' main.py '''

if __name__ == "__main__" :
	port = int(sys.argv[1])
	rpc = RPCTCP()
	rpc.addmethod('testmethod',testmethod)
	rpc.addmethod('testcode',testcode)
	rpc.addmethod('wp_consult',wp_consult)
	rpc.init({'port':port})

	#testcode({'lang':'sce','code':'a=input("");b=input("");printf("%g",a+b);','tests':[['1\n2\n','3\n']]})
	#print testcode({'lang':'cpp','code':'\nint soma(int a,int b){return a+b;}\n','tests':[['__TESTER__','http://homepages.dcc.ufmg.br/~sabir/tester_soma.zip']]})

