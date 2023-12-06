/**
 *
 * UserContainer
 *
 */

import PropTypes from "prop-types";
import React, { Fragment } from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import { GlobalFooter } from "ant-design-pro";
import { Col, Row, Select } from "antd";

import config from "../../../utils/config";

import { selectBuildingCluster } from "../../../redux/selectors";
import styles from "./index.less";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { changeLocale } from "containers/LanguageProvider/actions";
import { debounce } from "lodash";
const arr = [
  {
    value: "vi",
    name: "Tiếng Việt",
    name_en: "Vietnamese",
    icon: require("../../../images/icVn.svg"),
  },
  {
    value: "en",
    name: "Tiếng Anh",
    name_en: "English",
    icon: require("../../../images/icEn.svg"),
  },
];

/* eslint-disable react/prefer-stateless-function */
export class UserContainer extends React.PureComponent {
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
    console.log("sdada", this.props.language && this.props.language);

    const { buildingCluster, dispatch } = this.props;
    const { loading, data } = buildingCluster;
    let hotline = [];
    return (
      <Fragment>
        <div className={styles.UserContainerPage}>
          <div className={styles.changeLanguage}>
            <Select
              value={this.props.language && this.props.language}
              onChange={(value) => {
                dispatch(changeLocale(value));
              }}
              style={{ paddingRight: 5, width: "100%" }}
            >
              {arr.map((lll) => {
                return (
                  <Select.Option key={lll.value} value={lll.value}>
                    {this.props.language === "vi" ? (
                      <div style={{ alignItems: "center" }}>
                        <img
                          src={lll.icon}
                          style={{
                            marginRight: 6,
                          }}
                        ></img>
                        {lll.name}
                      </div>
                    ) : (
                      <div style={{ alignItems: "center" }}>
                        <img src={lll.icon} style={{ marginRight: 6 }}></img>
                        {lll.name_en}
                      </div>
                    )}
                  </Select.Option>
                );
              })}
            </Select>
          </div>
          <div className={styles.formMain}>
            <Row style={{ height: "100%" }}>
              <Col span={24} style={{ height: "100%" }}>
                <div className={styles.leftForm}>
                  {this.props.children}
                  <div className={styles.help}></div>
                </div>
              </Col>
              {/* <Col xl={14} lg={12} md={0} style={{ height: "100%" }}>
                <div className={styles.rightForm}>
                  <div
                    className={styles.firstRighForm}
                    style={{
                      backgroundImage: `url(${
                        !!data &&
                        !!data.medias &&
                        getFullLinkImage(data.medias.imageUrl)
                      })`,
                    }}
                  />
                  <Row type="flex" align="bottom" style={{ height: "100%" }}>
                    <Row className={styles.bgOpacity}>
                      <Row>
                        <Col
                          span={24}
                          style={{
                            fontWeight: "bold",
                            fontSize: 30,
                            color: "white",
                            zIndex: 100,
                          }}
                        >
                          <span>{data ? data.name : "..."}</span>
                        </Col>
                        <Col
                          style={{
                            fontSize: 14,
                            color: "white",
                            zIndex: 100,
                            marginTop: 16,
                          }}
                        >
                          <Row gutter={24}>
                            <Col span={6}>
                              <span>
                                <FormattedMessage {...messages.address} />
                              </span>
                            </Col>
                            <Col
                              span={18}
                              style={{ whiteSpace: "pre-wrap", paddingLeft: 8 }}
                            >
                              {(data ? data.address : "") || ""}
                            </Col>
                          </Row>
                        </Col>
                        <Col
                          style={{
                            fontSize: 14,
                            color: "white",
                            zIndex: 100,
                            marginTop: 4,
                          }}
                        >
                          <Row gutter={24}>
                            <Col span={6}>
                              <span>{"Hotline:"}</span>
                            </Col>
                            <Col
                              span={18}
                              style={{ whiteSpace: "pre-wrap", paddingLeft: 8 }}
                            >
                              {hotline
                                .map((hl) => `${hl.title}: ${hl.phone}`)
                                .join("\n")}
                            </Col>
                          </Row>
                        </Col>
                      </Row>
                    </Row>
                  </Row>
                </div>
              </Col> */}
            </Row>
          </div>
          <div className={styles.footer}>
            <GlobalFooter
              copyright={
                <span style={{ color: "white" }}>{config.copyright}</span>
              }
            />
          </div>
        </div>
      </Fragment>
    );
  }
}

UserContainer.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  buildingCluster: selectBuildingCluster(),
  language: makeSelectLocale(),
});
function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

export default compose(withConnect)(UserContainer);
