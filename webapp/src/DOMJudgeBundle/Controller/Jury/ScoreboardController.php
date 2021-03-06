<?php declare(strict_types=1);

namespace DOMJudgeBundle\Controller\Jury;

use DOMJudgeBundle\Service\DOMJudgeService;
use DOMJudgeBundle\Service\ScoreboardService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/jury/scoreboard")
 * @Security("has_role('ROLE_JURY')")
 */
class ScoreboardController extends Controller
{
    /**
     * @var DOMJudgeService
     */
    protected $dj;

    /**
     * @var ScoreboardService
     */
    protected $scoreboardService;

    /**
     * ScoreboardController constructor.
     * @param DOMJudgeService   $dj
     * @param ScoreboardService $scoreboardService
     */
    public function __construct(
        DOMJudgeService $dj,
        ScoreboardService $scoreboardService
    ) {
        $this->dj                = $dj;
        $this->scoreboardService = $scoreboardService;
    }

    /**
     * @Route("", name="jury_scoreboard")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function scoreboardAction(Request $request)
    {
        $response   = new Response();
        $refreshUrl = $this->generateUrl('jury_scoreboard');
        $contest    = $this->dj->getCurrentContest();
        $data       = $this->scoreboardService->getScoreboardTwigData($request, $response, $refreshUrl, true, false,
                                                                      false, $contest);

        if ($request->isXmlHttpRequest()) {
            return $this->render('@DOMJudge/partials/scoreboard.html.twig', $data, $response);
        }
        return $this->render('@DOMJudge/jury/scoreboard.html.twig', $data, $response);
    }
}
