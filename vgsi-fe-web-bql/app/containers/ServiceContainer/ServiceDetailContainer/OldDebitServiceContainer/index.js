/**
 *
 * OldDebitServiceContainer
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../../components/Page/Page";
import { Col, Menu, Row, Tooltip } from "antd";
import { Switch, Route, withRouter, Redirect } from "react-router-dom";

import LockFeePage from "./LockFeePage/Loadable";
import LockFeeTemplatePage from "./LockFeeTemplatePage/Loadable";
import { defaultAction, fetchDetailService } from "./actions";
import makeSelectOldDebitServiceContainer from "./selectors";
import { FormattedMessage } from "react-intl";
import messages from "./messages";

import("./index.less");

const KEY_MENU = {
  "/main/service/detail/old_debit/lock": "/main/service/detail/old_debit/lock",
  "/main/service/detail/old_debit/lock-template":
    "/main/service/detail/old_debit/lock-template",
};
/* eslint-disable react/prefer-stateless-function */
export class OldDebitServiceContainer extends React.PureComponent {
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
    const { oldDebitServiceContainer } = this.props;
    const { loading, data } = oldDebitServiceContainer;
    return (
      <Page className="OldDebitServiceContainer">
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
                <Menu.Item key="/main/service/detail/old_debit/lock">
                  <FormattedMessage {...messages.oldDeptApproved} />
                </Menu.Item>
                <Menu.Item key="/main/service/detail/old_debit/lock-template">
                  <FormattedMessage {...messages.oldDeptNotApproved} />
                </Menu.Item>
              </Menu>
            </Col>
            <Col md={19} lg={20}>
              <Switch>
                <Route
                  exact
                  path="/main/service/detail/old_debit/lock"
                  component={LockFeePage}
                />
                <Route
                  exact
                  path="/main/service/detail/old_debit/lock-template"
                  component={LockFeeTemplatePage}
                />
                <Redirect to="/main/service/detail/old_debit/lock" />
              </Switch>
            </Col>
          </Row>
        )}
      </Page>
    );
  }
}

OldDebitServiceContainer.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  oldDebitServiceContainer: makeSelectOldDebitServiceContainer(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "oldDebitServiceContainer", reducer });
const withSaga = injectSaga({ key: "oldDebitServiceContainer", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(OldDebitServiceContainer));
