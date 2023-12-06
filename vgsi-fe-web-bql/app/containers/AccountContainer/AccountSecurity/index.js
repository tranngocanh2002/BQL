/**
 *
 * AccountSecurity
 *
 */

import React, { Fragment } from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { FormattedMessage } from "react-intl";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectAccountSecurity from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import messages from "./messages";
import Page from "../../../components/Page/Page";
import { Row, List } from "antd";

/* eslint-disable react/prefer-stateless-function */
export class AccountSecurity extends React.PureComponent {
  render() {
    return (
      <Page inner noPadding>
        <Row>
          <span style={{ fontWeight: "bold", fontSize: 18 }}>
            <FormattedMessage {...messages.configSecurity} />
          </span>
          <List
            itemLayout="horizontal"
            dataSource={[
              {
                title: <FormattedMessage {...messages.password} />,
                description: (
                  <Fragment>
                    <FormattedMessage {...messages.passwordStrength} />
                    <font className="strong">
                      <FormattedMessage {...messages.strong} />
                    </font>
                  </Fragment>
                ),
                actions: [
                  <a
                    key="changePassword"
                    onClick={() => {
                      this.props.history.push(
                        "/main/account/settings/security/changepassword"
                      );
                    }}
                  >
                    <FormattedMessage {...messages.changePassword} />
                  </a>,
                ],
              },
            ]}
            renderItem={(item) => (
              <List.Item actions={item.actions}>
                <List.Item.Meta
                  title={item.title}
                  description={item.description}
                />
              </List.Item>
            )}
          />
        </Row>
      </Page>
    );
  }
}

AccountSecurity.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  accountSecurity: makeSelectAccountSecurity(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "accountSecurity", reducer });
const withSaga = injectSaga({ key: "accountSecurity", saga });

export default compose(withReducer, withSaga, withConnect)(AccountSecurity);
