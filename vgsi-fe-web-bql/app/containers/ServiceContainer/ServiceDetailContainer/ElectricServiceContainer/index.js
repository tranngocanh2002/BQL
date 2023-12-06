/**
 *
 * ElectricServiceContainer
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectElectricServiceContainer from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../../components/Page/Page";
import { Menu, Row, Col, Tooltip } from "antd";

import { Switch, Route, withRouter, Redirect } from "react-router-dom";

import InfoUsagePage from "./InfoUsage/Loadable";
import LockFeePage from "./LockFeePage/Loadable";
import LockFeeTemplatePage from "./LockFeeTemplatePage/Loadable";
import { defaultAction, fetchDetailService } from "./actions";
import { FormattedMessage, injectIntl } from "react-intl";
import messages from "./messages";

import("./index.less");

const KEY_MENU = {
  "/main/service/detail/electric/usage": "/main/service/detail/electric/usage",
  "/main/service/detail/electric/lock": "/main/service/detail/electric/lock",
  "/main/service/detail/electric/lock-template":
    "/main/service/detail/electric/lock-template",
};
/* eslint-disable react/prefer-stateless-function */
export class ElectricServiceContainer extends React.PureComponent {
  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }
  componentDidMount() {
    this.props.dispatch(fetchDetailService(`/${this.props.base_url}`));
  }

  handleClick = (e) => {
    this.props.history.push(e.key);
  };

  render() {
    const { electricServiceContainer } = this.props;
    const { loading, data } = electricServiceContainer;
    return (
      // ={loading || !!!data}
      <Page className="ElectricServiceContainer">
        {!(loading || !data) && (
          <Row
            gutter={30}
            style={{
              marginTop: 20,
            }}
          >
            <Col md={5} lg={4}>
              <Menu
                onClick={this.handleClick}
                selectedKeys={[KEY_MENU[this.props.location.pathname]]}
                className="leftmenu"
                mode="vertical-left"
              >
                <Menu.Item key="/main/service/detail/electric/usage">
                  <Tooltip title="Sử dụng">
                    <FormattedMessage {...messages.usage} />
                  </Tooltip>
                </Menu.Item>
                <Menu.Item key="/main/service/detail/electric/lock">
                  <Tooltip title=" Số điện đã duyệt">
                    <FormattedMessage {...messages.approvedElectricity} />
                  </Tooltip>
                </Menu.Item>
                <Menu.Item key="/main/service/detail/electric/lock-template">
                  <Tooltip title=" Số điện chưa duyệt">
                    <FormattedMessage {...messages.notApprovedElectricity} />
                  </Tooltip>
                </Menu.Item>
              </Menu>
            </Col>

            <Col md={19} lg={20}>
              <Switch>
                <Route
                  exact
                  path="/main/service/detail/electric/usage"
                  component={InfoUsagePage}
                />
                <Route
                  exact
                  path="/main/service/detail/electric/lock"
                  component={LockFeePage}
                />
                <Route
                  exact
                  path="/main/service/detail/electric/lock-template"
                  component={LockFeeTemplatePage}
                />
                <Redirect to="/main/service/detail/electric/usage" />
              </Switch>
            </Col>
          </Row>
        )}
      </Page>
    );
  }
}

ElectricServiceContainer.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  electricServiceContainer: makeSelectElectricServiceContainer(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "electricServiceContainer", reducer });
const withSaga = injectSaga({ key: "electricServiceContainer", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(ElectricServiceContainer)));
