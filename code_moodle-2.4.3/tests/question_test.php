<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/code/question.php');


/**
 * Unit tests for the Code question definition class.
 *
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_code_question_test extends advanced_testcase {
    public function test_compare_string_with_wildcard() {
        // Test case sensitive literal matches.
        $this->assertTrue((bool)qtype_code_question::compare_string_with_wildcard(
                'Frog', 'Frog', false));
        $this->assertFalse((bool)qtype_code_question::compare_string_with_wildcard(
                'Frog', 'frog', false));
        $this->assertTrue((bool)qtype_code_question::compare_string_with_wildcard(
                '   Frog   ', 'Frog', false));
        $this->assertFalse((bool)qtype_code_question::compare_string_with_wildcard(
                'Frogs', 'Frog', false));

        // Test case insensitive literal matches.
        $this->assertTrue((bool)qtype_code_question::compare_string_with_wildcard(
                'Frog', 'frog', true));
        $this->assertTrue((bool)qtype_code_question::compare_string_with_wildcard(
                '   FROG   ', 'Frog', true));
        $this->assertFalse((bool)qtype_code_question::compare_string_with_wildcard(
                'Frogs', 'Frog', true));

        // Test case sensitive wildcard matches.
        $this->assertTrue((bool)qtype_code_question::compare_string_with_wildcard(
                'Frog', 'F*og', false));
        $this->assertTrue((bool)qtype_code_question::compare_string_with_wildcard(
                'Fog', 'F*og', false));
        $this->assertTrue((bool)qtype_code_question::compare_string_with_wildcard(
                '   Fat dog   ', 'F*og', false));
        $this->assertFalse((bool)qtype_code_question::compare_string_with_wildcard(
                'Frogs', 'F*og', false));
        $this->assertFalse((bool)qtype_code_question::compare_string_with_wildcard(
                'Fg', 'F*og', false));
        $this->assertFalse((bool)qtype_code_question::compare_string_with_wildcard(
                'frog', 'F*og', false));
        $this->assertFalse((bool)qtype_code_question::compare_string_with_wildcard(
                '   fat dog   ', 'F*og', false));

        // Test case insensitive wildcard matches.
        $this->assertTrue((bool)qtype_code_question::compare_string_with_wildcard(
                'Frog', 'F*og', true));
        $this->assertTrue((bool)qtype_code_question::compare_string_with_wildcard(
                'Fog', 'F*og', true));
        $this->assertTrue((bool)qtype_code_question::compare_string_with_wildcard(
                '   Fat dog   ', 'F*og', true));
        $this->assertFalse((bool)qtype_code_question::compare_string_with_wildcard(
                'Frogs', 'F*og', true));
        $this->assertFalse((bool)qtype_code_question::compare_string_with_wildcard(
                'Fg', 'F*og', true));
        $this->assertTrue((bool)qtype_code_question::compare_string_with_wildcard(
                'frog', 'F*og', true));
        $this->assertTrue((bool)qtype_code_question::compare_string_with_wildcard(
                '   fat dog   ', 'F*og', true));

        // Test match using regexp special chars.
        $this->assertTrue((bool)qtype_code_question::compare_string_with_wildcard(
                '   *   ', '\*', false));
        $this->assertTrue((bool)qtype_code_question::compare_string_with_wildcard(
                '*', '\*', false));
        $this->assertTrue((bool)qtype_code_question::compare_string_with_wildcard(
                'Frog*toad', 'Frog\*toad', false));
        $this->assertFalse((bool)qtype_code_question::compare_string_with_wildcard(
                'a', '[a-z]', false));
        $this->assertTrue((bool)qtype_code_question::compare_string_with_wildcard(
                '[a-z]', '[a-z]', false));
        $this->assertTrue((bool)qtype_code_question::compare_string_with_wildcard(
                '\{}/', '\{}/', true));

        // See http://moodle.org/mod/forum/discuss.php?d=120557
        $this->assertTrue((bool)qtype_code_question::compare_string_with_wildcard(
                'ITÁLIE', 'Itálie', true));

        if (function_exists('normalizer_normalize')) {
            // Test ambiguous unicode representations
            $this->assertTrue((bool)qtype_code_question::compare_string_with_wildcard(
                    'départ', 'DÉPART', true));
            $this->assertFalse((bool)qtype_code_question::compare_string_with_wildcard(
                    'départ', 'DÉPART', false));
            $this->assertTrue((bool)qtype_code_question::compare_string_with_wildcard(
                    'd'."\xC3\xA9".'part', 'd'."\x65\xCC\x81".'part', false));
            $this->assertTrue((bool)qtype_code_question::compare_string_with_wildcard(
                    'd'."\xC3\xA9".'part', 'D'."\x45\xCC\x81".'PART', true));
        }
    }

    public function test_is_complete_response() {
        $question = test_question_maker::make_question('code');

        $this->assertFalse($question->is_complete_response(array()));
        $this->assertFalse($question->is_complete_response(array('answer' => '')));
        $this->assertTrue($question->is_complete_response(array('answer' => '0')));
        $this->assertTrue($question->is_complete_response(array('answer' => '0.0')));
        $this->assertTrue($question->is_complete_response(array('answer' => 'x')));
    }

    public function test_is_gradable_response() {
        $question = test_question_maker::make_question('code');

        $this->assertFalse($question->is_gradable_response(array()));
        $this->assertFalse($question->is_gradable_response(array('answer' => '')));
        $this->assertTrue($question->is_gradable_response(array('answer' => '0')));
        $this->assertTrue($question->is_gradable_response(array('answer' => '0.0')));
        $this->assertTrue($question->is_gradable_response(array('answer' => 'x')));
    }

    public function test_grading() {
        $question = test_question_maker::make_question('code');

        $this->assertEquals(array(0, question_state::$gradedwrong),
                $question->grade_response(array('answer' => 'x')));
        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => 'frog')));
        $this->assertEquals(array(0.8, question_state::$gradedpartial),
                $question->grade_response(array('answer' => 'toad')));
    }

    public function test_get_correct_response() {
        $question = test_question_maker::make_question('code');

        $this->assertEquals(array('answer' => 'frog'),
                $question->get_correct_response());
    }

    public function test_get_correct_response_escapedwildcards() {
        $question = test_question_maker::make_question('code', 'escapedwildcards');

        $this->assertEquals(array('answer' => 'x*y'), $question->get_correct_response());
    }

    public function test_get_question_summary() {
        $sa = test_question_maker::make_question('code');
        $qsummary = $sa->get_question_summary();
        $this->assertEquals('Name an amphibian: __________', $qsummary);
    }

    public function test_summarise_response() {
        $sa = test_question_maker::make_question('code');
        $summary = $sa->summarise_response(array('answer' => 'dog'));
        $this->assertEquals('dog', $summary);
    }

    public function test_classify_response() {
        $sa = test_question_maker::make_question('code');
        $sa->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(
                new question_classified_response(13, 'frog', 1.0)),
                $sa->classify_response(array('answer' => 'frog')));
        $this->assertEquals(array(
                new question_classified_response(14, 'toad', 0.8)),
                $sa->classify_response(array('answer' => 'toad')));
        $this->assertEquals(array(
                new question_classified_response(15, 'cat', 0.0)),
                $sa->classify_response(array('answer' => 'cat')));
        $this->assertEquals(array(
                question_classified_response::no_response()),
                $sa->classify_response(array('answer' => '')));
    }

    public function test_classify_response_no_star() {
        $sa = test_question_maker::make_question('code', 'frogonly');
        $sa->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(
                new question_classified_response(13, 'frog', 1.0)),
                $sa->classify_response(array('answer' => 'frog')));
        $this->assertEquals(array(
                new question_classified_response(0, 'toad', 0.0)),
                $sa->classify_response(array('answer' => 'toad')));
        $this->assertEquals(array(
                question_classified_response::no_response()),
                $sa->classify_response(array('answer' => '')));
    }
}
