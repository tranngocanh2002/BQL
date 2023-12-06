/**
 *
 * historyAccessControl
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { FormattedMessage } from "react-intl";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";
import _ from "lodash";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelecthistoryAccessControl from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import messages from "./messages";
import queryString from "query-string";
import { defaultAction, fetchAllHistory, fetchApartment } from "./actions";
import styles from "./index.less";
import { Table, Row, Tooltip, Button } from "antd";
import Page from "../../components/Page/Page";
import moment from "moment";
import { getFullLinkImage } from "../../connection";

/* eslint-disable react/prefer-stateless-function */
export class historyAccessControl extends React.PureComponent {
  state = {
    current: 1,
    currentEdit: undefined,
    visible: false,
    filter: {},
    downloading: false,
  };
  onSearch = (keyword) => {
    this.props.dispatch(fetchApartment({ keyword }));
  };
  _onSearch = _.debounce(this.onSearch, 300);

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
    this._unmounted = true;
  }

  componentDidMount() {
    this.reload(this.props.location.search);
    this._onSearch("");
  }

  componentWillReceiveProps(nextProps) {
    const { search } = nextProps.location;
    if (this.props.location.search != search) {
      this.reload(search);
    }
  }

  reload = (search) => {
    let params = queryString.parse(search);
    try {
      if (params.page) params.page = parseInt(params.page);
      else params.page = 1;
    } catch (error) {
      params.page = 1;
    }
    this.setState({ current: params.page, filter: params }, () => {
      this.props.dispatch(fetchAllHistory(params));
    });
  };

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState(
      {
        sort,
        current: pagination.current,
      },
      () => {
        this.props.history.push(
          `/main/history/carpacking?${queryString.stringify({
            ...this.state.filter,
            page: this.state.current,
          })}`
        );
      }
    );
  };

  render() {
    const { auth_group, historyAccessControl } = this.props;
    const { loading, data, totalPage, apartment } = historyAccessControl;
    const { current, filter } = this.state;
    const columns = [
      {
        width: 50,
        align: "center",
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => (
          <span>
            {Math.max(0, loading ? current - 2 : current - 1) * 20 + index + 1}
          </span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.status} />
          </span>
        ),
        dataIndex: "type",
        key: "type",
        render: (text) => {
          if (text == 0) {
            return (
              <span className="luci-status-primary">
                <FormattedMessage {...messages.resident} />
              </span>
            );
          }
          if (text == 1) {
            return (
              <span className="luci-status-error">
                <FormattedMessage {...messages.guest} />
              </span>
            );
          }
        },
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.name} />
          </span>
        ),
        dataIndex: "resident_user_name",
        key: "resident_user_name",
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.time} />
          </span>
        ),
        dataIndex: "time_event",
        key: "time_event",
        render: (text) => moment.unix(text).format("HH:mm DD/MM/YYYY"),
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.image} />
          </span>
        ),
        dataIndex: "image_uri",
        key: "image_uri",
        render: (text) => {
          return (
            <a
              href={getFullLinkImage(text)}
              rel="noopener noreferrer"
              target="_blank"
            >
              <img
                src={getFullLinkImage(text)}
                style={{ width: 80, height: 80 }}
              />
            </a>
          );
        },
      },
    ];

    return (
      <Page inner className={styles.historyAccessControlListPage}>
        <div>
          {/* <Row style={{ paddingBottom: 16 }} type='flex' align='middle' >
            <Col span={4} style={{ marginRight: 10 }} >
              <Input.Search
                value={filter['number'] || ''}
                placeholder="Tìm kiếm số biển số"
                onChange={e => {
                  this.setState({
                    filter: {
                      ...filter,
                      ['number']: e.target.value
                    }
                  })
                }}
              />
            </Col>
            <Col span={4} style={{ marginRight: 10 }} >
              <Select
                style={{ width: '100%' }}
                loading={apartment.loading}
                showSearch
                placeholder="Chọn căn hộ"
                optionFilterProp="children"
                notFoundContent={apartment.loading ? <Spin size="small" /> : null}
                onSearch={this._onSearch}
                onChange={value => {
                  console.log('value', value)
                  this.setState({
                    filter: {
                      ...filter,
                      ['apartment_id']: value
                    }
                  }, () => {
                    // this.props.history.push(`/main/finance/fees?${queryString.stringify({
                    //   ...this.state.filter,
                    //   page: 1,
                    // })}`)
                  })
                }}
                allowClear
                value={filter['apartment_id']}
              >
                {
                  apartment.lst.map(gr => {
                    return <Select.Option key={`group-${gr.id}`} value={`${gr.id}`}>{`${gr.name} (${gr.parent_path})`}</Select.Option>
                  })
                }
              </Select>
            </Col>
            <Col span={6} style={{ marginRight: 10 }} >
              <DatePicker.RangePicker
                key='picker'
                placeholder={['Ngày bắt đầu', 'Ngày kết thúc']}
                format="DD/MM/YYYY"
                // disabledDate={(current) => {
                //   // Can not select days before today and today
                //   return current && current > moment().endOf('day');
                // }}
                value={[filter.start_datetime ? moment.unix(filter.start_datetime) : undefined, filter.end_datetime ? moment.unix(filter.end_datetime) : undefined]}
                onChange={(value1, value2) => {
                  this.setState({
                    filter: {
                      ...filter,
                      ['start_datetime']: value1[0] ? value1[0].unix() : undefined,
                      ['end_datetime']: value1[1] ? value1[1].unix() : undefined,
                    }
                  })
                }}
              />
            </Col>
            <Button type='primary' onClick={e => {
              this.props.history.push(`/main/history/carpacking?${queryString.stringify({
                ...this.state.filter,
                page: 1,
              })}`)
            }} >
              Tìm kiếm
                </Button>
          </Row> */}
          <Row style={{ paddingBottom: 16 }}>
            <Tooltip title={<FormattedMessage {...messages.reload} />}>
              <Button
                shape="circle-outline"
                style={{ padding: 0, marginRight: 10 }}
                onClick={() => {
                  this.reload(this.props.location.search);
                }}
                icon="reload"
                size="large"
              />
            </Tooltip>
          </Row>
          <Table
            rowKey="id"
            loading={loading}
            columns={columns}
            dataSource={data}
            bordered
            locale={{ emptyText: <FormattedMessage {...messages.noData} /> }}
            pagination={{
              pageSize: 20,
              total: totalPage,
              current,
              showTotal: (total) => (
                <FormattedMessage {...messages.total} values={{ total }} />
              ),
            }}
            onChange={this.handleTableChange}
          />
        </div>
      </Page>
    );
  }
}

historyAccessControl.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  historyAccessControl: makeSelecthistoryAccessControl(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "historyAccessControl", reducer });
const withSaga = injectSaga({ key: "historyAccessControl", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(historyAccessControl);
