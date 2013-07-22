<?php

defined('MOODLE_INTERNAL') || die();

class qtype_code_question extends question_graded_by_strategy
        implements question_response_answer_comparer {
    /** @var boolean whether answers should be graded case-sensitively. */
    public $usecase;
    /** @var array of question_answer. */
    public $answers = array();

    public function __construct() {
        parent::__construct(new question_first_matching_answer_grading_strategy($this));
    }

    public function get_expected_data() {
        return array('answer' => PARAM_RAW_TRIMMED);
    }

    public function summarise_response(array $response) {
        if (isset($response['answer'])) {
            return $response['answer'];
        } else {
            return null;
        }
    }

    public function is_complete_response(array $response) {
	///sabir:
	//return 1;

        return array_key_exists('answer', $response) &&
                ($response['answer'] || $response['answer'] === '0');
    }

    public function get_validation_error(array $response) {
        if ($this->is_gradable_response($response)) {
            return '';
        }
        return get_string('pleaseenterananswer', 'qtype_code');
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        return question_utils::arrays_same_at_key_missing_is_blank(
                $prevresponse, $newresponse, 'answer');
    }

    public function get_answers() {
        return $this->answers;
    }

///sabir:
public function http_post ($url, $data)
{
    $data_url = http_build_query ($data);
    $data_len = strlen ($data_url);

    return array (
	'content'=>file_get_contents (
		$url, false, stream_context_create (
			array (
				'http'=>array (
					'method'=>'POST',
					'header'=>"Connection: close\r\nContent-Length: $data_len\r\n",
					'content'=>$data_url
				)
			)
		)
	)
    );
}

    ///sabir:
    function get_lang_from_last_line($code)
    {
	$lines = explode("\n", $code);
	$lastline = $lines[count($lines)-1];
	$c = array('//cpp' => 'cpp','//sce' => 'sce', '#py' => 'py');
	$lang = $c[$lastline];
	if (!$lang) $lang = 'cpp';
	//die($lang);
	return $lang;
    }

    ///sabir:testa código
    public function testcode($usercode,$testset)
    {

	// contornado última comparação
	try {
		//echo "<script>alert('".addslashes(json_encode($usercode)).'\n'.addslashes(json_encode($testset))."');</script>";
		if (addslashes(json_encode($usercode)) === addslashes(json_encode($testset))) return 1;
	} catch (Exception $e) {
	}

	//$testset='[["1\n2\n","3\n"],["2\n3\n","4\n"]]'; // TODO apagar isso
	//echo $resp;
	//echo $answer;

	// TODO
	// store both files in /tmp/mdl_code.log

	//$url = "http://localhost:8000/quap/testcode/";
	$url = "http://www.latin.dcc.ufmg.br/srlab/quap/testcode/";
	//$url = "http://localhost/quap/testcode/";

	$lang = self::get_lang_from_last_line($usercode);

	$data = array(
			'lang'	=> $lang,
			'code'	=> $usercode,
			'tests'	=> $testset); // '[["1\n2\n","3\n"],["2\n3\n","4\n"]]'

	$data_url = http_build_query ($data);

	$data_len = strlen ($data_url);

	// {"score": 0.5, "params": {"lang": "py", "tests": [["1\n2\n", "3\n"], ["2\n3\n", "4\n"]], "code": "a = int(raw_input(''))\r\nb = int(raw_input(''))\r\nprint a+b\r\n"}, "method": "testcode", "result": {"success": [true, false]}}
	$result = file_get_contents($url,false,stream_context_create(array('http'=>array('method'=>'post','content'=>$data_url))),null);

	$resultjson = json_decode($result);

	//echo "<script>alert('".addslashes($resultjson->score).'\n'.addslashes($result).'\n'.addslashes($data_url)."');</script>";

	// por enquanto apenas scores binários, o usuário tem que acertar tudo!
	// na questão deve haver apenas um teste
	if ($resultjson->score == 1)
		return 1;
	else
		return 0;

	if ($usercode == $testset)
	{
            return 1;
	}
	return 0;
    }

    public function compare_response_with_answer(array $response, question_answer $answer) {
        if (!array_key_exists('answer', $response) || is_null($response['answer'])) {
            return false;
        }

	///sabir:
	$a = self::testcode($response['answer'], $answer->answer);
        //$a = self::compare_string_with_wildcard(
        //        $response['answer'], $answer->answer, !$this->usecase);
	//echo "<script>alert('".$response['answer'].'\n'.$answer->answer.'\n'.$a."');</script>";
	//echo "<script>alert('".'\n'.$answer->answer.'\n'.$a."');</script>";
	return $a;

        //return self::compare_string_with_wildcard(
        //        $response['answer'], $answer->answer, !$this->usecase);
    }

    public static function compare_string_with_wildcard($string, $pattern, $ignorecase) {

        // Normalise any non-canonical UTF-8 characters before we start.
        $pattern = self::safe_normalize($pattern);
        $string = self::safe_normalize($string);

        // Break the string on non-escaped asterisks.
        $bits = preg_split('/(?<!\\\\)\*/', $pattern);
        // Escape regexp special characters in the bits.
        $excapedbits = array();
        foreach ($bits as $bit) {
            $excapedbits[] = preg_quote(str_replace('\*', '*', $bit));
        }
        // Put it back together to make the regexp.
        $regexp = '|^' . implode('.*', $excapedbits) . '$|u';

        // Make the match insensitive if requested to.
        if ($ignorecase) {
            $regexp .= 'i';
        }

        return preg_match($regexp, trim($string));
    }

    /**
     * Normalise a UTf-8 string to FORM_C, avoiding the pitfalls in PHP's
     * normalizer_normalize function.
     * @param string $string the input string.
     * @return string the normalised string.
     */
    protected static function safe_normalize($string) {
	///sabir:
	return $string;


        if (!$string) {
            return '';
        }

        if (!function_exists('normalizer_normalize')) {
            return $string;
        }

        $normalised = normalizer_normalize($string, Normalizer::FORM_C);
        if (!$normalised) {
            // An error occurred in normalizer_normalize, but we have no idea what.
            debugging('Failed to normalise string: ' . $string, DEBUG_DEVELOPER);
            return $string; // Return the original string, since it is the best we have.
        }

        return $normalised;
    }

    public function get_correct_response() {
        $response = parent::get_correct_response();
        if ($response) {
            $response['answer'] = $this->clean_response($response['answer']);
        }
        return $response;
    }

    public function clean_response($answer) {

	///sabir:
	return $answer;


        // Break the string on non-escaped asterisks.
        $bits = preg_split('/(?<!\\\\)\*/', $answer);

        // Unescape *s in the bits.
        $cleanbits = array();
        foreach ($bits as $bit) {
            $cleanbits[] = str_replace('\*', '*', $bit);
        }

        // Put it back together with spaces to look nice.
        return trim(implode(' ', $cleanbits));
    }

    public function check_file_access($qa, $options, $component, $filearea,
            $args, $forcedownload) {
        if ($component == 'question' && $filearea == 'answerfeedback') {
            $currentanswer = $qa->get_last_qt_var('answer');
            $answer = $this->get_matching_answer(array('answer' => $currentanswer));
            $answerid = reset($args); // itemid is answer id.
            return $options->feedback && $answer && $answerid == $answer->id;

        } else if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);

        } else {
            return parent::check_file_access($qa, $options, $component, $filearea,
                    $args, $forcedownload);
        }
    }
}
