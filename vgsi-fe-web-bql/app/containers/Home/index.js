/**
 *
 * Dashboard
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import { Row } from "antd";
import Exception from "ant-design-pro/lib/Exception";
import makeSelectDashboard from "./selectors";
import reducer from "./reducer";
import saga from "./saga";

import CardApartment from "./CardApartment";
import CardResident from "./CardResident";
import CardRequest from "./CardRequest";
import CardBookingCount from "./CardBookingCount";
import CardServiceUtilityForm from "./CardServiceUtilityForm";
import CardHandoverStatus from "./CardHandoverStatus";

import ChartRequest from "./ChartRequest";
import ChartBooking from "./ChartBooking";
import ChartFinance from "./ChartFinance";
import ChartApartment from "./ChartApartment";
import ChartResident from "./ChartResident";

import Page from "../../components/Page/Page";
import WithRole from "../../components/WithRole";
import {
  fetchCountReport,
  fetchRequest,
  fetchFinance,
  fetchApartment,
  fetchResident,
  fetchBookingRevenue,
  fetchMaintenance,
} from "./actions";
import { selectAuthGroup } from "../../redux/selectors";
import { config } from "../../utils";
import { debounce } from "lodash";
import { FormattedMessage, injectIntl } from "react-intl";
import messages from "./messages";
import moment from "moment";
import ChartMaintenance from "./ChartMaintenance";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { Redirect } from "react-router-dom";
const RowResponsiveProps = {
  style: {
    overflowX: "auto",
    flexWrap: "nowrap",
    display: "flex",
    margin: 10,
    marginBottom: 16,
  },
};

const topColResponsiveProps = {
  style: {
    paddingRight: 16,
    paddingBottom: 16,
  },
  md: 12,
  lg: 10,
  xl: 8,
  xxl: 6,
};

// const maintain = {
//   loading: false,
//   data: [
//     {
//       month: moment().subtract(6, "month").startOf("month").unix(),
//       fix: 0,
//       not_fix: 0,
//     },
//     {
//       month: moment().subtract(5, "month").startOf("month").unix(),
//       fix: 4,
//       not_fix: 2,
//     },
//     {
//       month: moment().subtract(4, "month").startOf("month").unix(),
//       fix: 2,
//       not_fix: 1,
//     },
//     {
//       month: moment().subtract(3, "month").startOf("month").unix(),
//       fix: 3,
//       not_fix: 0,
//     },
//     {
//       month: moment().subtract(2, "month").startOf("month").unix(),
//       fix: 1,
//       not_fix: 0,
//     },
//     {
//       month: moment().subtract(1, "month").startOf("month").unix(),
//       fix: 0,
//       not_fix: 0,
//     },
//     {
//       month: moment().startOf("month").unix(),
//       fix: 0,
//       not_fix: 0,
//     },
//   ],
//   from_month: moment().subtract(6, "month").startOf("month").unix(),
//   to_month: moment().startOf("month").unix(),
// };

/* eslint-disable react/prefer-stateless-function */
export class Dashboard extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = { windowWidth: window.innerWidth };
  }
  handleResize = () => {
    this.setState({ windowWidth: window.innerWidth });
  };
  componentDidMount() {
    window.addEventListener("resize", debounce(this.handleResize, 300));
    if (
      this.props.auth_group.checkRole([config.ALL_ROLE_NAME.DASHBOARD_RESIDENT])
    ) {
      this.props.dispatch(fetchResident());
    }
    if (
      this.props.auth_group.checkRole([config.ALL_ROLE_NAME.DASHBOARD_GENERAL])
    ) {
      this.props.dispatch(fetchCountReport());
    }
    if (
      this.props.auth_group.checkRole([config.ALL_ROLE_NAME.DASHBOARD_REQUEST])
    ) {
      this.props.dispatch(fetchRequest());
    }
    if (
      this.props.auth_group.checkRole([
        config.ALL_ROLE_NAME.DASHBOARD_MAINTENANCE,
      ])
    ) {
      this.props.dispatch(fetchMaintenance());
    }
    if (
      this.props.auth_group.checkRole([config.ALL_ROLE_NAME.DASHBOARD_FINANCE])
    ) {
      this.props.dispatch(fetchFinance());
    }
    if (
      this.props.auth_group.checkRole([config.ALL_ROLE_NAME.DASHBOARD_FINANCE])
    ) {
      this.props.dispatch(fetchBookingRevenue());
    }
    if (
      this.props.auth_group.checkRole([
        config.ALL_ROLE_NAME.DASHBOARD_APARTMENT,
      ])
    ) {
      this.props.dispatch(fetchApartment());
    }
  }
  componentWillReceiveProps(nextProps) {
    if (nextProps.auth_group.data_role !== this.props.auth_group.data_role) {
      if (
        nextProps.auth_group.checkRole([
          config.ALL_ROLE_NAME.DASHBOARD_RESIDENT,
        ])
      ) {
        nextProps.dispatch(fetchResident());
      }
      if (
        nextProps.auth_group.checkRole([config.ALL_ROLE_NAME.DASHBOARD_GENERAL])
      ) {
        nextProps.dispatch(fetchCountReport());
      }
      if (
        nextProps.auth_group.checkRole([config.ALL_ROLE_NAME.DASHBOARD_REQUEST])
      ) {
        nextProps.dispatch(fetchRequest());
      }
      if (
        nextProps.auth_group.checkRole([
          config.ALL_ROLE_NAME.DASHBOARD_MAINTENANCE,
        ])
      ) {
        nextProps.dispatch(fetchMaintenance());
      }
      if (
        nextProps.auth_group.checkRole([config.ALL_ROLE_NAME.DASHBOARD_FINANCE])
      ) {
        nextProps.dispatch(fetchFinance());
      }
      if (
        nextProps.auth_group.checkRole([
          config.ALL_ROLE_NAME.DASHBOARD_APARTMENT,
        ])
      ) {
        nextProps.dispatch(fetchApartment());
      }
      if (
        nextProps.auth_group.checkRole([config.ALL_ROLE_NAME.DASHBOARD_FINANCE])
      ) {
        nextProps.dispatch(fetchBookingRevenue());
      }
    }
  }
  componentWillUnMount() {
    window.removeEventListener("resize", debounce(this.handleResize, 300));
  }

  render() {
    const { windowWidth } = this.state;
    const { dashboard, dispatch, history, auth_group, intl } = this.props;
    const {
      countAll,
      request,
      finance,
      maintenance,
      apartment,
      resident,
      booking,
    } = dashboard;

    const topColResponsivePropsChart = {
      intl: intl,
      md: 24,
      xl: 12,
      style: { marginBottom: 24 },
    };
    if (
      !auth_group.checkRole([
        config.ALL_ROLE_NAME.DASHBOARD_RESIDENT,
        config.ALL_ROLE_NAME.DASHBOARD_GENERAL,
        config.ALL_ROLE_NAME.DASHBOARD_REQUEST,
        config.ALL_ROLE_NAME.DASHBOARD_FINANCE,
        config.ALL_ROLE_NAME.DASHBOARD_APARTMENT,
      ])
    ) {
      return (
        <Redirect to="/main/infomation" />
        // <Page inner>
        //   <Exception
        //     type="404"
        //     desc={<FormattedMessage {...messages.errorView} />}
        //     actions={[]}
        //   />
        // </Page>
      );
    }
    return (
      <Page loading={false}>
        <Row gutter={24}>
          <WithRole roles={[config.ALL_ROLE_NAME.DASHBOARD_GENERAL]}>
            <Row {...RowResponsiveProps}>
              <CardApartment
                {...topColResponsiveProps}
                intl={intl}
                loading={countAll.loading}
                apartment={countAll.data ? countAll.data.apartment : undefined}
                history={history}
                auth_group={auth_group}
                screenwidth={windowWidth}
              />
              <CardResident
                {...topColResponsiveProps}
                intl={intl}
                loading={countAll.loading}
                history={history}
                auth_group={auth_group}
                resident={countAll.data ? countAll.data.resident : undefined}
                screenwidth={windowWidth}
              />
              <CardRequest
                {...topColResponsiveProps}
                intl={intl}
                loading={countAll.loading}
                history={history}
                auth_group={auth_group}
                request={countAll.data ? countAll.data.request : undefined}
                screenwidth={windowWidth}
              />
              <CardBookingCount
                {...topColResponsiveProps}
                intl={intl}
                loading={countAll.loading}
                history={history}
                auth_group={auth_group}
                booking={
                  countAll.data ? countAll.data.service_booking : undefined
                }
                screenwidth={windowWidth}
              />
              <CardServiceUtilityForm
                {...topColResponsiveProps}
                intl={intl}
                loading={countAll.loading}
                history={history}
                auth_group={auth_group}
                service_utility_form={
                  countAll.data ? countAll.data.service_utility_form : undefined
                }
                screenwidth={windowWidth}
              />
              <CardHandoverStatus
                {...topColResponsiveProps}
                intl={intl}
                loading={countAll.loading}
                apartment={countAll.data ? countAll.data.apartment : undefined}
                history={history}
                auth_group={auth_group}
                screenwidth={windowWidth}
              />
            </Row>
          </WithRole>

          <WithRole roles={[config.ALL_ROLE_NAME.DASHBOARD_REQUEST]}>
            <ChartRequest
              language={this.props.language}
              {...topColResponsivePropsChart}
              request={request}
              dispatch={dispatch}
              history={history}
              auth_group={auth_group}
              screenwidth={windowWidth}
            />
          </WithRole>
          <WithRole roles={[config.ALL_ROLE_NAME.DASHBOARD_MAINTENANCE]}>
            <ChartMaintenance
              {...topColResponsivePropsChart}
              maintenance={maintenance}
              dispatch={dispatch}
              history={history}
              auth_group={auth_group}
              screenwidth={windowWidth}
            />
          </WithRole>
          <WithRole roles={[config.ALL_ROLE_NAME.DASHBOARD_FINANCE]}>
            <ChartFinance
              {...topColResponsivePropsChart}
              lg={12}
              xl={15}
              finance={finance}
              dispatch={dispatch}
              history={history}
              auth_group={auth_group}
              screenwidth={windowWidth}
            />
          </WithRole>
          <WithRole roles={[config.ALL_ROLE_NAME.DASHBOARD_FINANCE]}>
            <ChartBooking
              {...topColResponsivePropsChart}
              lg={12}
              xl={9}
              booking={booking}
              dispatch={dispatch}
              language={this.props.language}
              history={history}
              auth_group={auth_group}
              screenwidth={windowWidth}
            />
          </WithRole>
          <WithRole roles={[config.ALL_ROLE_NAME.DASHBOARD_APARTMENT]}>
            <ChartApartment
              {...topColResponsivePropsChart}
              language={this.props.language}
              apartment={apartment}
              history={history}
              dispatch={dispatch}
              auth_group={auth_group}
              screenwidth={windowWidth}
            />
          </WithRole>
          <WithRole roles={[config.ALL_ROLE_NAME.DASHBOARD_RESIDENT]}>
            <ChartResident
              {...topColResponsivePropsChart}
              resident={resident}
              history={history}
              dispatch={dispatch}
              loading={resident.loading}
              auth_group={auth_group}
              screenwidth={windowWidth}
            />
          </WithRole>
        </Row>
      </Page>
    );
  }
}

Dashboard.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  dashboard: makeSelectDashboard(),
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "dashboard", reducer });
const withSaga = injectSaga({ key: "dashboard", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(Dashboard));
