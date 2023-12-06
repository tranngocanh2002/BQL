/**
 *
 * ResourceManager
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { FormattedMessage, injectIntl } from "react-intl";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectResourceManager from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import messages from "../messages";
import Page from "../../../components/Page/Page";
import { Row, Col, Menu } from "antd";
import { Switch, Route, withRouter, Redirect } from "react-router-dom";
import { selectBuildingCluster, selectCity } from "../../../redux/selectors";
import EmailPage from "./EmailPage";
import NotificationPage from "./NotificationPage";
import SMSNotificationPage from "./SMSNotificationPage";
import { fetchBuildingCluster } from "../../../redux/actions/config";
import { defaultAction } from "./actions";

/* eslint-disable react/prefer-stateless-function */
export class ResourceManager extends React.PureComponent {
  componentDidMount() {
    this.props.dispatch(fetchBuildingCluster());
  }
  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  render() {
    const { data } = this.props.buildingCluster;
    const formatMessage = this.props.intl.formatMessage;
    return (
      <div>
        <Page noMinHeight inner>
          <Row style={{ padding: 16 }}>
            {/* <span>{formatMessage(messages.SMS)}:</span>{" "}
            <strong>{data.limit_sms}</strong>
            <span>{` ${formatMessage(messages.messagePerMonth)} (`}</span>
            <strong>{data.sms_price}</strong>
            <span>{" vnd/sms)"}</span>
            <br />
            <br /> */}
            <span>{formatMessage(messages.emailService)}:</span>{" "}
            <strong>{data.limit_email}</strong>
            <span>{` ${formatMessage(messages.limitEmail)}`}</span>
            <br />
            <br />
            <span>{formatMessage(messages.notificationService)}:</span>{" "}
            <strong>{data.limit_notify}</strong>
            <span>{` ${formatMessage(messages.limitEmail)}`}</span>
          </Row>
        </Page>
        <Page noMinHeight inner style={{ marginTop: 16 }}>
          <div>
            <Menu
              mode={"horizontal"}
              selectedKeys={[this.props.location.pathname]}
              onSelect={({ key }) => {
                this.props.history.push(key);
              }}
            >
              <Menu.Item key={"/main/setting/resources/email"}>Email</Menu.Item>
              <Menu.Item key={"/main/setting/resources/notification"}>
                Notification
              </Menu.Item>
              {/* <Menu.Item key={"/main/setting/resources/sms"}>SMS</Menu.Item> */}
            </Menu>
            <Switch>
              <Route
                exact
                path="/main/setting/resources/email"
                component={EmailPage}
              />
              <Route
                exact
                path="/main/setting/resources/notification"
                component={NotificationPage}
              />
              {/* <Route
                exact
                path="/main/setting/resources/sms"
                component={SMSNotificationPage}
              /> */}
              <Redirect to="/main/setting/resources/email" />
            </Switch>
          </div>
        </Page>
      </div>
    );
  }
}

ResourceManager.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  // resourceManager: makeSelectResourceManager()
  buildingCluster: selectBuildingCluster(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "resourceManager", reducer });
const withSaga = injectSaga({ key: "resourceManager", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(ResourceManager));
