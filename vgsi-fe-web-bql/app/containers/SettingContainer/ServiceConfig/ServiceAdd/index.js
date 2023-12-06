/**
 *
 * ServiceAdd
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import { Button, Form, Input, Row, Select, Spin } from "antd";
import _ from "lodash";
import { injectIntl } from "react-intl";
import { Redirect } from "react-router";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../../components/Page/Page";
import { config } from "../../../../utils";
import {
  addService,
  defaultAction,
  fetchDetailServiceCloud,
  fetchServiceProvider,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectServiceAdd from "./selectors";

const formItemLayout = {
  labelCol: {
    span: 5,
  },
  wrapperCol: {
    span: 17,
  },
};
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class ServiceAdd extends React.PureComponent {
  constructor(props) {
    super(props);

    const { record } = props.location.state || {};

    this.state = {
      record,
    };

    this._onSearch = _.debounce(this.onSearch, 300);
  }

  onSearch = (keyword) => {
    this.props.dispatch(fetchServiceProvider({ name: keyword }));
  };
  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    const { id } = this.props.match.params;
    if (id != undefined && !this.state.record) {
      this.props.dispatch(fetchDetailServiceCloud({ id }));
    }
    this._onSearch("");
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.serviceAdd.detail.loading !=
        nextProps.serviceAdd.detail.loading &&
      !nextProps.serviceAdd.detail.loading
    ) {
      this.setState({
        record: nextProps.serviceAdd.detail.data,
      });
    }
  }

  _onAdd = () => {
    const { dispatch, form } = this.props;
    const { record } = this.state;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }

      dispatch(
        addService({
          ...values,
          service_id: record.id,
          service_description: record.description,
          status: 1,
          medias: {
            logo: record.logo,
          },
        })
      );
    });
  };

  render() {
    const { serviceAdd } = this.props;
    const { record } = this.state;
    const { getFieldDecorator } = this.props.form;

    if (serviceAdd.success) {
      return <Redirect to="/main/service/cloud" />;
    }

    return (
      <Page inner loading={!record || serviceAdd.detail.loading}>
        <Row style={{ paddingTop: 24 }}>
          {!!record && (
            <Form {...formItemLayout}>
              <Form.Item label={"Tên dịch vụ"}>
                {getFieldDecorator("service_name", {
                  initialValue: record ? record.name : "",
                  rules: [
                    {
                      required: true,
                      message: "Tên dịch vụ không được để trống.",
                      whitespace: true,
                    },
                  ],
                })(<Input />)}
              </Form.Item>
              <Form.Item label={"Nhà cung cấp"}>
                {getFieldDecorator("service_provider_id", {
                  // initialValue: !!record ? record.title : '',
                  rules: [
                    {
                      required: true,
                      message: "Nhà cung cấp không được để trống.",
                      whitespace: true,
                    },
                  ],
                })(
                  <Select
                    loading={serviceAdd.provider.loading}
                    showSearch
                    placeholder="Chọn nhà cung cấp"
                    optionFilterProp="children"
                    notFoundContent={
                      serviceAdd.provider.loading ? <Spin size="small" /> : null
                    }
                    onSearch={this._onSearch}
                  >
                    {serviceAdd.provider.items.map((gr) => {
                      return (
                        <Select.Option
                          disabled={gr.status == 0}
                          key={`group-${gr.id}`}
                          value={`${gr.id}`}
                        >
                          {gr.name}
                          {gr.status == 0 && (
                            <span
                              style={{ fontWeight: "lighter", fontSize: 12 }}
                            >{` (${config.STATUS_SERVICE_PROVIDER[0].name})`}</span>
                          )}
                        </Select.Option>
                      );
                    })}
                  </Select>
                )}
              </Form.Item>
              <Form.Item
                label={"Giới thiệu"}
                style={{ whiteSpace: "pre-wrap" }}
              >
                <p
                  dangerouslySetInnerHTML={{
                    __html: record.description.replace(
                      /(&nbsp;|<([^>]+)>)/gi,
                      ""
                    ),
                  }}
                  style={{
                    whiteSpace: "pre-wrap",
                    paddingTop: 10,
                  }}
                />
              </Form.Item>
              <Form.Item label={<span>&ensp;&ensp;</span>} colon={false}>
                <Button
                  type="danger"
                  style={{ width: 100 }}
                  disabled={serviceAdd.adding}
                  onClick={() => {
                    this.props.history.push("/main/service/cloud");
                  }}
                >
                  Quay lại
                </Button>
                <Button
                  ghost
                  type="primary"
                  style={{ width: 100, marginLeft: 10 }}
                  loading={serviceAdd.adding}
                  onClick={this._onAdd}
                >
                  Thêm
                </Button>
              </Form.Item>
            </Form>
          )}
        </Row>
      </Page>
    );
  }
}

ServiceAdd.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  serviceAdd: makeSelectServiceAdd(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "serviceAdd", reducer });
const withSaga = injectSaga({ key: "serviceAdd", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(ServiceAdd));
