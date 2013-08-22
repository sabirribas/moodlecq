<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/code/questiontype.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the code question type class.
 *
 * @copyright  2007 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_code_test extends advanced_testcase {
    public static $includecoverage = array(
        'question/type/questiontypebase.php',
        'question/type/code/questiontype.php',
    );

    protected $qtype;

    protected function setUp() {
        $this->qtype = new qtype_code();
    }

    protected function tearDown() {
        $this->qtype = null;
    }

    protected function get_test_question_data() {
        return test_question_maker::get_question_data('code');
    }

    public function test_name() {
        $this->assertEquals($this->qtype->name(), 'code');
    }

    public function test_can_analyse_responses() {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    public function test_get_random_guess_score() {
        $q = test_question_maker::get_question_data('code');
        $q->options->answers[15]->fraction = 0.1;
        $this->assertEquals(0.1, $this->qtype->get_random_guess_score($q));
    }

    public function test_get_possible_responses() {
        $q = test_question_maker::get_question_data('code');

        $this->assertEquals(array(
            $q->id => array(
                13 => new question_possible_response('frog', 1),
                14 => new question_possible_response('toad', 0.8),
                15 => new question_possible_response('*', 0),
                null => question_possible_response::no_response()
            ),
        ), $this->qtype->get_possible_responses($q));
    }

    public function test_get_possible_responses_no_star() {
        $q = test_question_maker::get_question_data('code', 'frogonly');

        $this->assertEquals(array(
            $q->id => array(
                13 => new question_possible_response('frog', 1),
                0 => new question_possible_response(get_string('didnotmatchanyanswer', 'question'), 0),
                null => question_possible_response::no_response()
            ),
        ), $this->qtype->get_possible_responses($q));
    }
}
