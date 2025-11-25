<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class PostGenerator
{
    private Client $httpClient;
    private array $topics = [
        // STILL - Mental Health & Mindset (Stabilization, Awareness, Naming Reality)
        'When Rejection Feels Like Proof You\'re Worthless: The Stoic Dichotomy of Control',
        'The Time Panic: Why You Feel Like You\'re Already Behind at 30',
        'When Nothing Feels Good Anymore: Understanding Anhedonia and Emotional Numbness',
        'She Left and You Don\'t Know Who You Are: Codependency and Identity Loss',
        'Seeing Peers Thrive While You Fall Behind: The Comparison Trap',
        'Existing on Autopilot: When You\'re a Shell of Your Former Self',
        'The 35-Year Cutoff Myth: When You Believe Past Mistakes Made Success Impossible',
        'Your Nervous System is Stuck: Why Everything Feels Like a Threat',
        
        // GRIT - Habits & Discipline (Execution, Repetition, Daily Practice)
        'Breaking Free from Porn and Weed: A 90-Day Neurobiological Reset',
        'Fumbling in Interviews: How Addiction Hijacks Your Confidence',
        'The Habit Loop: Replacing Escape Routes with Better Routines',
        'Why Everything Feels Like Hard Mode: Burnout Despite Doing Everything Right',
        'Behavioral Activation: How to Act Before You Feel Like It',
        'The Energy Envelope: Why Pushing Through Makes You Crash',
        'Exposure Therapy for Social Anxiety: Confidence Comes After, Not Before',
        'Dopamine Detox: 30 Days to Recalibrate Your Reward System',
        
        // REFLECTION - Purpose & Meaning (Making Sense, Narrative, Values Clarity)
        'The Late Bloomer\'s Reframe: You\'re Not Defective, You\'re Delayed',
        'Learning to Be an Adult at 28: When You Missed Developmental Milestones',
        'Social Skills as Skills: Why Awkwardness is Lack of Reps, Not Character',
        'Re-Authoring Your Story: From "Stunted Adult" to "Adult Learner"',
        'Identity Foreclosure: When the Mask You Wore Cracks and You Don\'t Know Who\'s Underneath',
        'Making Sense of Lost Years: Constructing a Coherent Narrative Without Shame',
        'From Fixed to Growth: Shifting "I Can\'t" to "I Don\'t Know How Yet"',
        'Values Work When You\'re Numb: What Matters When Nothing Feels Good',
        
        // ASCEND - Career & Growth (Building, Creating, Strategic Action)
        'Career Pivots After 30: Research Says You\'re Not Too Late',
        'Building Skills That Compound: The Late Starter\'s Advantage',
        'The Leverage Ladder: Trading Time for Impact in Your 30s',
        'Skill Stacking for Late Bloomers: Why Generalists Win Long-Term',
        'How to Bet on Yourself When You Have Responsibilities',
        'From Freeze to Flow: Translating Nervous System Regulation Into Career Moves',
        'The Second Act: Real Stories of People Who Started Over at 35+',
        'Strategic Growth After Burnout: Building Sustainable Momentum'
    ];

    private int $topicIndex = 0;

    public function __construct()
    {
        $this->httpClient = new Client([
            'timeout' => 60,
            'headers' => [
                'x-api-key' => CLAUDE_API_KEY,
                'anthropic-version' => CLAUDE_API_VERSION,
                'content-type' => 'application/json',
            ]
        ]);
    }

    public function generatePost(): array
    {
        $topic = $this->getNextTopic();
        
        $prompt = $this->buildPrompt($topic);
        
        try {
            $response = $this->callClaudeAPI($prompt);
            $content = $this->parseResponse($response);
            
            // Extract or generate excerpt
            $excerpt = $this->generateExcerpt($content);
            
            return [
                'title' => $topic,
                'content' => $content,
                'excerpt' => $excerpt,
                'success' => true
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function getNextTopic(): string
    {
        // Randomly select a topic to keep content fresh
        return $this->topics[array_rand($this->topics)];
    }

    private function buildPrompt(string $topic): string
    {
        // Generate varied opening scenarios to avoid repetition
        $openingScenarios = [
            "You're scrolling LinkedIn at 11 PM, watching everyone else win.",
            "It's Sunday evening and that familiar dread is creeping in.",
            "You just declined another invitation because you 'need to figure things out first.'",
            "You're sitting at your desk, staring at the same task for the third day in a row.",
            "You tell yourself 'tomorrow' again, knowing you said that yesterday too.",
            "You're in a meeting, nodding along, thinking 'is this it?'",
            "Your friend just launched something. You've been 'planning' for months.",
            "You opened a new note titled 'Life Plan' for the fourth time this year.",
            "It's 6 AM, you hit snooze for the fifth time, and you hate yourself a little more.",
            "You're at a bar, half-listening, wondering why you feel so disconnected.",
            "You see your reflection in your laptop screen during a Zoom call and barely recognize yourself.",
            "Friday night used to mean something. Now it's just two days closer to Monday.",
            "You've got 47 browser tabs open and none of them are getting you closer to where you want to be.",
            "You're in the shower, rehearsing conversations you'll never have.",
            "Your calendar is full but you're not actually building anything.",
            "You just bought another course you probably won't finish."
        ];
        
        $randomScenario = $openingScenarios[array_rand($openingScenarios)];
        $uniqueId = substr(md5($topic . time()), 0, 8);
        
        return <<<PROMPT
You are writing for "Stuck in Adulthood" - a blog for ambitious men in their 20s and 30s who feel stuck, behind in life, or directionless but want to evolve.

Write a blog post about: "{$topic}"

CRITICAL - ORIGINALITY REQUIREMENTS:
- DO NOT use the "2:47 AM can't sleep" scenario - this has been overused
- DO NOT start with clichéd "staring at the ceiling" or "scrolling at night" openings
- Each post must feel completely unique, not like a template with swapped words
- Use unexpected angles, fresh metaphors, and specific modern situations
- Avoid generic self-help language - be raw, specific, and real

OPTIONAL OPENING HOOK (use this OR create your own unique one):
"{$randomScenario}"

VOICE & STYLE:
- Use Robert Oliver's emotional temperature and rhythm - talk directly to ONE guy, like a late-night conversation
- Include SPECIFIC, UNIQUE scenes that make this feel real and visceral (not generic scenarios)
- Write with James Clear's clarity and Naval Ravikant's philosophical depth
- Make it feel like a conversation with the version of yourself that refuses to stay stuck

FRAMEWORKS - GROUND ADVICE IN ESTABLISHED PSYCHOLOGY/PHILOSOPHY:
You MUST incorporate at least one of these evidence-based frameworks and explain HOW it addresses this specific problem:

**For STILL phase topics** (emotional crisis, overwhelm, numbness):
- Stoicism (dichotomy of control, amor fati, preferred indifferents)
- Self-Compassion (Kristin Neff - treat yourself like a friend)
- CBT Cognitive Distortions (naming thinking errors without dismissing pain)
- Polyvagal Theory (nervous system states - shutdown, fight/flight, social engagement)
- Attachment Theory (how childhood patterns show up in adult reactions)

**For GRIT phase topics** (habits, discipline, taking action):
- Behavioral Activation (action before feeling)
- Habit Loop (Cue-Routine-Reward, replacing not resisting)
- Exposure Therapy (graduated exposure to feared situations)
- Dopamine Baseline Science (why addiction kills motivation for everything else)
- Energy Envelope Theory (pacing, not pushing through)

**For REFLECTION phase topics** (identity, narrative, meaning):
- Narrative Therapy (re-authoring your story)
- Growth Mindset (abilities aren't fixed)
- Developmental Psychology (late bloomer reframe)
- Identity Foreclosure (performing a role vs being yourself)
- Values Clarification (ACT - what matters regardless of feelings)

**For ASCEND phase topics** (building, creating, career):
- Skill Acquisition Research (deliberate practice, expertise science)
- Leverage Principles (Archimedes - force multipliers)
- Career Construction Theory (designing not finding careers)
- Compounding Returns (how small consistent actions multiply)

CRITICAL: Don't just name-drop frameworks. Explain:
1. What the framework says (in plain language)
2. HOW it applies to this exact problem
3. What the person should DO with this understanding

STRUCTURE:
- Start with an emotional hook - a SPECIFIC, ORIGINAL scene/feeling/moment (see suggestions above)
- Name what's happening - give language to the experience using psychological/philosophical frameworks
- Explain the framework - what it says and HOW it addresses this specific problem
- Build the mindset reframe - shift how they see themselves and their situation
- End with 3-5 clear, tactical steps they can take TODAY (phase-appropriate)
- Length: 700-900 words
- Don't use markdown headers, just write flowing paragraphs with natural breaks
- Make it conversational, not academic - explain frameworks like you're talking to a friend

TONE:
- Direct but not preachy
- Empathetic but challenging
- Philosophical but practical
- Like you've been there and made it through
- Fresh, not formulaic

THE FIFTH STATE FRAMEWORK (understand the phase-appropriate goal):
- STILL: Stabilization, awareness, naming what's happening - NOT fixing or optimizing yet. Goal: "Get through today without making it worse."
- GRIT: Execution, repetition, showing up to the practice - even when it sucks. Goal: "Do the thing X days in a row, regardless of feeling."
- REFLECTION: Making sense, narrative coherence, values clarity - NOT taking big action yet. Goal: "Understand why I'm here and what matters to me."
- ASCEND: Building, creating, strategic moves - translating inner work into external results. Goal: "Execute on the clarity and momentum I've built."

Match your advice to the phase. Don't tell someone in STILL to "start a side hustle." Don't tell someone in ASCEND to "just sit with the feeling."

VARIETY NOTE: This is post #{$uniqueId}. Make it distinctly different from other posts in tone, opening, and approach. Avoid repeating any scenarios, metaphors, or structures you might use in other posts.

Write the complete blog post now:
PROMPT;
    }

    private function callClaudeAPI(string $prompt): string
    {
        try {
            $response = $this->httpClient->post(CLAUDE_API_URL, [
                'json' => [
                    'model' => CLAUDE_MODEL,
                    'max_tokens' => 2048,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ]
                ]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            
            if (isset($body['content'][0]['text'])) {
                return $body['content'][0]['text'];
            }
            
            throw new \RuntimeException('Unexpected API response format');
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Claude API request failed: ' . $e->getMessage());
        }
    }

    private function parseResponse(string $response): string
    {
        // Clean up the response
        $content = trim($response);
        
        // Convert line breaks to proper HTML paragraphs
        $paragraphs = array_filter(explode("\n\n", $content));
        $html = '';
        
        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if (!empty($paragraph)) {
                $html .= '<p>' . nl2br(htmlspecialchars($paragraph)) . '</p>' . "\n";
            }
        }
        
        return $html;
    }

    private function generateExcerpt(string $content): string
    {
        // Strip HTML tags and get first 200 characters
        $text = strip_tags($content);
        // Decode HTML entities (like &#039; back to ')
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);
        
        if (strlen($text) > 200) {
            $text = substr($text, 0, 200);
            $text = substr($text, 0, strrpos($text, ' ')) . '...';
        }
        
        return $text;
    }
}

