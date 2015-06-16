package app.controllers;

import app.models.*;
import com.google.common.base.Optional;
import digirent.jersey.inject.UserContext;
import digirent.jpa.JPA.Pagination;
import digirent.view.View;

import javax.ws.rs.*;
import javax.ws.rs.core.Context;
import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.Response;
import javax.ws.rs.core.UriInfo;

import static digirent.util.ParameterUtils.params;

@Path("/dashboard")
@Produces(MediaType.TEXT_HTML)
public class DashboardController {
    private final QuestionDao questionDao;
    private final AnswerDao answerDao;

    public DashboardController(
            QuestionDao questionDao,
            AnswerDao answerDao) {
        this.questionDao = questionDao;
        this.answerDao = answerDao;
    }

    @GET
    public Response index(
            @UserContext UserHelper userHelper,
            @Context UriInfo uriInfo) {

        Optional<Account> accountOpt = userHelper.getAccount();
        if (!accountOpt.isPresent())
            return redirectToLogin(uriInfo, "/dashboard");
        Account account = accountOpt.get();

        return Response.ok(new View("dashboard/index", params(
                "account", account))).build();
    }

    @GET
    @Path("questions")
    public Response questions(
            @UserContext UserHelper userHelper,
            @Context UriInfo uriInfo,
            @QueryParam("offset") @DefaultValue("0") int offset,
            @QueryParam("limit") @DefaultValue("20") int limit) {

        Optional<Account> accountOpt = userHelper.getAccount();
        if (!accountOpt.isPresent())
            return redirectToLogin(uriInfo, "/dashboard/questions");
        Account account = accountOpt.get();

        Pagination<QuestionsResult> questions =
                questionDao.selectByAuthorId(account.getId(), offset, limit);
        return Response.ok(new View("dashboard/questions", params(
                "account", account,
                "questions", questions))).build();
    }

    @GET
    @Path("answers")
    public Response answers(
            @UserContext UserHelper userHelper,
            @Context UriInfo uriInfo,
            @QueryParam("offset") @DefaultValue("0") int offset,
            @QueryParam("limit") @DefaultValue("20") int limit) {

        Optional<Account> accountOpt = userHelper.getAccount();
        if (!accountOpt.isPresent())
            return redirectToLogin(uriInfo, "/dashboard/answers");
        Account account = accountOpt.get();

        Pagination<AnswersResult> answers =
                answerDao.selectByAuthorId(account.getId(), offset, limit);
        return Response.ok(new View("dashboard/answers", params(
                "account", account,
                "answers", answers))).build();
    }

    private Response redirectToLogin(UriInfo uriInfo, String returnTo) {
        return Response.seeOther(uriInfo.getBaseUriBuilder()
                .path("/accounts/signin")
                .queryParam("returnTo", returnTo)
                .build()).build();
    }
}
