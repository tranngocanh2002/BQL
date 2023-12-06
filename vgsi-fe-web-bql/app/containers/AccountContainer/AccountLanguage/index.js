/**
 *
 * AccountSecurity
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { FormattedMessage } from "react-intl";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import reducer from "./reducer";
import saga from "./saga";
import messages from "./messages";
import Page from "../../../components/Page/Page";
import { Row, Col, Select, Button } from "antd";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { changeLocale } from "containers/LanguageProvider/actions";

const arr = [
  {
    value: "vi",
    name: "Tiếng Việt",
    name_en: "Vietnamese",
  },
  {
    value: "en",
    name: "Tiếng Anh",
    name_en: "English",
  },
];

/* eslint-disable react/prefer-stateless-function */
export class AccountLanguage extends React.PureComponent {
  state = {
    language: "vi",
  };
  componentWillReceiveProps(nextProps) {
    if (nextProps.language !== this.props.language) {
      window.location.reload();
    }
    if (nextProps.language !== this.state.language) {
      this.setState({
        language: nextProps.language,
      });
    }
  }
  render() {
    const { dispatch } = this.props;
    return (
      <Page inner noPadding>
        <Row
          style={{
            display: "flex",
            flexDirection: "column",
          }}
        >
          <span style={{ fontWeight: "bold", fontSize: 18 }}>
            <FormattedMessage {...messages.language} />
          </span>
          <p style={{ fontSize: 16, marginTop: 12 }}>
            <FormattedMessage {...messages.chooseLanguagePlaceholder} />
          </p>
          <Col md={14} xl={10}>
            <Select
              style={{ width: "60%" }}
              value={this.state.language}
              onChange={(value) => {
                this.setState({
                  language: value,
                });
              }}
            >
              {arr.map((lll) => {
                return (
                  <Select.Option key={lll.value} value={lll.value}>
                    {this.props.language === "vi" ? lll.name : lll.name_en}
                  </Select.Option>
                );
              })}
            </Select>
          </Col>
          <p style={{ fontSize: 16, marginTop: 12 }}>
            (*) <FormattedMessage {...messages.chooseLanguageTooltip} />
          </p>
          <Button
            type="primary"
            style={{ marginTop: 12, width: 120 }}
            onClick={() => {
              dispatch(changeLocale(this.state.language));
            }}
          >
            <FormattedMessage {...messages.update} />
          </Button>
        </Row>
      </Page>
    );
  }
}

AccountLanguage.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "accountSecurity", reducer });
const withSaga = injectSaga({ key: "accountSecurity", saga });

export default compose(withReducer, withSaga, withConnect)(AccountLanguage);
