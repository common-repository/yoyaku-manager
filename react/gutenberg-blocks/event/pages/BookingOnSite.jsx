import { frontAddEventBooking, useTicketsWithSoldCountForFront } from "@/api";
import {
  APIErrorMessage,
  LoadingData,
  showErrorToast,
} from "@/components/common";
import {
  AcceptTermsOfServiceField,
  BookingDatetimeLabel,
  CustomerForm,
  FormHeader,
  TicketsForm,
} from "@/gutenberg-blocks/components/event";
import { BasePage } from "@/gutenberg-blocks/components/layout";
import { HandleError } from "@/pages/others";
import { gatewayType } from "@/utils/consts";
import { settings } from "@/utils/settings";
import { useEffect, useRef, useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { Button, Form, Spinner } from "react-bootstrap";
import ReCAPTCHA from "react-google-recaptcha";
import { useFieldArray, useForm } from "react-hook-form";
import { useNavigate, useParams } from "react-router-dom";
import { Element, scroller } from "react-scroll";

/**
 * 予約フォーム
 * 注）チケットが無い、または全て売り切れの場合でもフォームが表示される。購入チケットが1枚も無い場合はフォームのバリデーションエラーになる。
 * @param eventData
 * @return {JSX.Element}
 * @constructor
 */
export const BookingOnSite = ({ eventData }) => {
  const [acceptTermsOfService, setAcceptTermsOfService] = useState(false);
  const [defaultValue, setDefaultValue] = useState([]);
  const [errorResponse, setErrorResponse] = useState();
  const { uuid } = useParams();
  const navigate = useNavigate();
  const maxTotalTickets = parseInt(eventData.max_tickets_per_booking);
  const selectedPeriod = eventData.periods.find(
    (period) => period.uuid === uuid,
  );
  const {
    data: tickets,
    error,
    isLoading: isLoadingTickets,
    mutate: mutateTickets,
  } = useTicketsWithSoldCountForFront(uuid);
  const [useFixedTicket, setUseFixedTicket] = useState(true);
  const {
    control,
    register,
    setValue,
    getValues,
    watch,
    handleSubmit,
    formState: { errors, isSubmitting },
  } = useForm({ values: defaultValue });
  const { fields: ticketFields } = useFieldArray({ control, name: "tickets" });
  const watchTickets = watch("tickets");

  // 初回描画時と、フォームエラー時にticketsの更新が発生する。フォームエラー時はチケットの値だけをリセットするようにする。
  useEffect(() => {
    if (tickets.items.length) {
      const formValues = getValues();
      // ticketフォームを使うならtrue, チケット1種類かつ最大1枚ならfalse
      const _useFixedTicket =
        1 === maxTotalTickets && 1 === tickets.items.length;
      const initBuyTickets = tickets.items.map((ticket) => {
        return { ...ticket, buy_count: _useFixedTicket ? 1 : 0 };
      });

      setDefaultValue({
        ...formValues,
        gateway: gatewayType.on_site,
        event_period_uuid: uuid,
        tickets: initBuyTickets,
      });
      setUseFixedTicket(_useFixedTicket);
    }
  }, [tickets]);

  // recaptcha
  const reCaptchaSiteKey = settings.google_recaptcha_site_key;
  const recaptchaRef = useRef();

  const onSubmit = async (data) => {
    let captchaValue = "";
    if (reCaptchaSiteKey) {
      captchaValue = await recaptchaRef.current.executeAsync();
    }

    const result = await frontAddEventBooking({
      ...data,
      captcha_value: captchaValue,
    });

    if (result.code === 400) {
      // チケットが完売している可能性があるため、データを更新する
      await mutateTickets(tickets);
      setErrorResponse(result);
      recaptchaRef.current.reset();
      scroller.scrollTo("scrollToElement");
    } else if (400 < result.code || result.code < 600) {
      // 受付終了や、満席などイベント選択期間画面に戻る必要がある場合
      navigate("/");
      showErrorToast(result.message);
    } else if (result?.redirect_url) {
      window.location.href = result.redirect_url;
    } else {
      navigate("/completion");
    }
  };

  if (!selectedPeriod.rest_ticket_count) {
    navigate("/");
  }
  if (error) return <HandleError error={error} />;
  if (isLoadingTickets) return <LoadingData />;

  return (
    <BasePage>
      <FormHeader order={2} />
      <Element name="scrollToElement">
        <BookingDatetimeLabel startDatetime={selectedPeriod.start_datetime} />
      </Element>

      {errorResponse && <APIErrorMessage errorResponse={errorResponse} />}

      <Form noValidate onSubmit={handleSubmit(onSubmit)}>
        {reCaptchaSiteKey && (
          <ReCAPTCHA
            ref={recaptchaRef}
            size="invisible"
            sitekey={reCaptchaSiteKey}
          />
        )}
        <div>
          <CustomerForm
            errors={errors}
            register={register}
            setValue={setValue}
            watchPhone={watch("phone")}
          />

          {watchTickets.length && (
            <TicketsForm
              buyTickets={watchTickets}
              fields={ticketFields}
              errors={errors}
              register={register}
              maxTotalTickets={maxTotalTickets}
              useFixedTicket={useFixedTicket}
            />
          )}

          <AcceptTermsOfServiceField
            acceptTermsOfService={acceptTermsOfService}
            changeAcceptTermsOfService={() =>
              setAcceptTermsOfService(!acceptTermsOfService)
            }
          />
        </div>

        <div className="mt-3" style={{ textAlign: "center" }}>
          <Button
            type="submit"
            variant="primary"
            size="lg"
            disabled={!acceptTermsOfService || isSubmitting}
          >
            {isSubmitting && (
              <Spinner
                className="me-1"
                as="span"
                animation="border"
                role="status"
                aria-hidden="true"
              />
            )}
            {__("Submit", "yoyaku-manager")}
          </Button>
        </div>
      </Form>
    </BasePage>
  );
};
